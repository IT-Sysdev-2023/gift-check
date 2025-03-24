<?php

namespace App\Http\Controllers;

use Svg\Tag\Rect;
use App\Models\Gc;
use App\Models\User;
use Inertia\Inertia;
use App\Models\Store;
use App\Models\Customer;
use App\Models\AccessPage;
use App\Models\CreditCard;
use App\Models\StoreStaff;
use App\Models\Denomination;
use Illuminate\Http\Request;
use App\Helpers\ColumnHelper;
use Illuminate\Validation\Rule;
use App\Models\InstitutCustomer;
use App\Models\PromoGcReleaseToItem;
use function Laravel\Prompts\search;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Hash;
use App\Services\Admin\AdminServices;

use App\Services\Admin\DBTransaction;
use Illuminate\Validation\getPromoTag;
use App\Models\SpecialExternalCustomer;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Requests\PurchaseOrderRequest;
use App\Models\SpecialExternalGcrequestEmpAssign;
use App\Services\Treasury\Transactions\SpecialGcPaymentService;
use Illuminate\Support\Facades\DB;
use Symfony\Contracts\Service\Attribute\Required;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function __construct(public AdminServices $adminservices, public DBTransaction $dBTransaction)
    {
    }
    public function submitNewUsername(Request $request)
    {
        // dd($request->all());
        // dd($request->params['id']);
        $password = User::where('user_id', $request->params['id'])->first();
        $currentPass = $password->password;
        $currentUsername = $password->username;

        if (Hash::check($request->params['password']['password'], $currentPass)) {
            $currentUsername = $password->username;
            if ($currentUsername == $request->params['username']) {
                return response()->json([
                    'error' => true,
                    'message' => 'Username is not change, please update first'
                ]);
            } else {
                $data = User::where('user_id', $request->params['id'])
                    ->update([
                        'username' => $request->params['username']
                    ]);
                if ($data) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Username change successfully'
                    ]);
                } else {
                    return response()->json([
                        'error' => true,
                        'message' => 'Username change successfully'
                    ]);
                }
            }
        } else {
            return response()->json([
                'error' => true,
                'message' => 'Your password dont match, please try again'
            ]);
        }
    }

    public function submitNewPassword(Request $request)
    {
        $data = $request->validate([
            'old_password' => 'required',
            'new_password' => 'required',
            'confirm_password' => 'required'
        ]);

        $inputOldPassword = $request->old_password;

        $oldPassword = User::where('user_id', $request->id)->first();
        $getPassword = $oldPassword->password;

        if (Hash::check($inputOldPassword, $getPassword)) {
            if ($request->new_password != $request->confirm_password) {
                return response()->json([
                    'error' => true,
                    'message' => 'Password dont match, please try again.'
                ]);
            } else {
                $data = User::where('user_id', $request->id)->update([
                    'password' => Hash::make($request->new_password)
                ]);
                if ($data) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Password updated successfully'
                    ]);
                }
            }
        } else {
            return response()->json([
                'error' => true,
                'message' => 'Old password is incorrect, please try again.'
            ]);
        }
    }

    public function deactivateUser(Request $request)
    {
        $checkStatus = User::where('user_id', $request->id)->first();
        $status = $checkStatus->user_status === 'active' ? 'inactive' : 'active';

        $checkStatus->update([
            'user_status' => $status
        ]);
        return back()->with([
            'success' => "Status {$status}d successfully"
        ]);
    }

    public function index()
    {
        $users = User::count();
        $currentYear = date('Y');
        $currentMonth = date('m');

        // This query is for the total sales per store
        $query = DB::table('stores')
            ->leftJoin('transaction_stores', 'stores.store_id', '=', 'transaction_stores.trans_store')
            ->leftJoin('transaction_sales', 'transaction_sales.sales_transaction_id', '=', 'transaction_stores.trans_sid')
            ->leftJoin('gc', 'gc.barcode_no', '=', 'transaction_sales.sales_barcode')
            ->leftJoin('denomination', 'denomination.denom_id', '=', 'gc.denom_id')
            ->select(
                'stores.store_name',
                DB::raw("IFNULL(COUNT(transaction_sales.sales_barcode), 0) as gc_count")
            )
            ->where(function ($query) use ($currentYear, $currentMonth) {
                $query->whereYear('transaction_stores.trans_datetime', $currentYear)
                    ->whereMonth('transaction_stores.trans_datetime', $currentMonth);
            })
            ->orWhereNull('transaction_stores.trans_sid')
            ->groupBy('stores.store_name')
            ->get();

        // This query is for special gc Per month
        $specialGcQuery = DB::table('special_external_gcrequest')
            ->join('special_external_customer', 'special_external_customer.spcus_id', '=', 'special_external_gcrequest.spexgc_company')
            ->leftJoin('approved_request', 'approved_request.reqap_trid', '=', 'special_external_gcrequest.spexgc_id')
            ->where('special_external_gcrequest.spexgc_status', 'approved')
            ->whereYear('approved_request.reqap_date', $currentYear)
            ->select(
                DB::raw("MONTH(approved_request.reqap_date) as month"),
                DB::raw("COUNT(*) as total_count")
            )
            ->groupBy(DB::raw("MONTH(approved_request.reqap_date)"))
            ->orderBy(DB::raw("MONTH(approved_request.reqap_date)"))
            ->get();

        // This query is for regular gc per month
        $institutionQuery = DB::table('institut_transactions')
            ->join('institut_customer', 'institut_customer.ins_id', '=', 'institut_transactions.institutr_cusid')
            ->select(
                DB::raw("MONTH(institut_transactions.institutr_date) as month"),
                DB::raw("COUNT(*) as total_count")
            )
            ->whereYear('institut_transactions.institutr_date', $currentYear)
            ->groupBy(DB::raw("MONTH(institut_transactions.institutr_date)"));

        $storesQuery = DB::table('approved_gcrequest')
            ->join('store_gcrequest', 'store_gcrequest.sgc_id', '=', 'approved_gcrequest.agcr_request_id')
            ->join('stores', 'stores.store_id', '=', 'store_gcrequest.sgc_store')
            ->select(
                DB::raw("MONTH(approved_gcrequest.agcr_approved_at) as month"),
                DB::raw("COUNT(*) as total_count")
            )
            ->whereYear('approved_gcrequest.agcr_approved_at', $currentYear)
            ->groupBy(DB::raw("MONTH(approved_gcrequest.agcr_approved_at)"))
            ->unionAll($institutionQuery);

        $specialQuery = DB::table('special_external_gcrequest')
            ->join('special_external_customer', 'special_external_customer.spcus_id', '=', 'special_external_gcrequest.spexgc_company')
            ->select(
                DB::raw("MONTH(special_external_gcrequest.spexgc_datereq) as month"),
                DB::raw("COUNT(*) as total_count")
            )
            ->whereYear('special_external_gcrequest.spexgc_datereq', $currentYear)
            ->groupBy(DB::raw("MONTH(special_external_gcrequest.spexgc_datereq)"))
            ->unionAll($storesQuery);

        //use this code if the error is mysql version error
        $specialGcData = collect($specialGcQuery);
        $storesData = collect(DB::select($storesQuery->toSql(), $storesQuery->getBindings()));
        $institutionData = collect(DB::select($institutionQuery->toSql(), $institutionQuery->getBindings()));

        // Manually merge the collections
        $mergedData = $specialGcData->merge($storesData)->merge($institutionData);

        // Perform final aggregation in Laravel instead of MySQL
        $finalQuery = $mergedData->groupBy('month')->map(function ($group) {
            return [
                'month' => $group->first()->month,
                'total_transactions' => $group->sum('total_count'),
            ];
        })->values();

        //change this one above code given
        // $finalQuery = DB::table(DB::raw("({$specialQuery->toSql()}) as merged"))
        //     ->mergeBindings($specialQuery)
        //     ->select(
        //         'month',
        //         DB::raw("SUM(total_count) as total_transactions")
        //     )
        //     ->groupBy('month')
        //     ->orderBy('month')
        //     ->get();
        return inertia('Admin/AdminDashboard', [
            'users' => $users,
            'data' => $query,
            'specialGcData' => $specialGcQuery,
            'regularGcData' => $finalQuery
        ]);
    }

    public function statusScanner(Request $request)
    {
        $data = $this->adminservices->statusScanned($request);

        return Inertia::render('Admin/StatusScanner', [
            'data' => $data->steps,
            'latestStatus' => 0,
            'transType' => $data->transType,
            'statusBarcode' => $data->barcodeNotFound,
            'empty' => $data->empty,
            'success' => $data->success,
            'barcode' => $request->barcode,
            'fetch' => $request->fetch
        ]);
    }
    public function scanGcStatusIndex()
    {
        return Inertia::render('Admin/ScanGcStatuses');
    }
    public function barcodeStatus()
    {
    }



    public function purchaseOrderDetails()
    {
        return inertia('Admin/PurchaseOrderDetails', [
            'columns' => ColumnHelper::$purchase_details_columns,
            'record' => $this->adminservices->purchaseOrderDetails(),
            'podetails' => $this->adminservices->getpodetailsDatabase(),
        ]);
    }

    // public function submitPurchaseOrders(PurchaseOrderRequest $request)
    // {
    //     $denomination = collect($request->denom)->filter(function ($item) {
    //         return $item !== null;
    //     });

    //     return $this->dBTransaction->createPruchaseOrders($request, $denomination);
    // }

    public function userlist(Request $request)
    {

        // dd($request->store);
        $Store = Store::get();
        $access_page = AccessPage::get();

        $usersQuery = User::select(
            'users.user_id',
            'stores.store_id',
            'users.emp_id',
            'users.username',
            'users.firstname',
            'users.lastname',
            'users.usertype',
            'users.login',
            'users.user_status',
            'users.date_created',
            'access_page.title',
            'stores.store_name',
            'users.store_assigned',
            'access_page.employee_type',
            'users.user_role',
            'users.it_type',
            'users.retail_group',
            'users.password'
        )
            ->join('access_page', 'users.usertype', '=', 'access_page.access_no')
            ->leftJoin('stores', 'users.store_assigned', '=', 'stores.store_id');

        $searchTerm = $request['searchData'];
        if ($searchTerm) {
            $usersQuery->where(function ($query) use ($searchTerm) {
                $query->where('users.username', 'like', '%' . $searchTerm . '%')
                    ->orWhere('users.firstname', 'like', '%' . $searchTerm . '%')
                    ->orWhere('users.lastname', 'like', '%' . $searchTerm . '%')
                    ->orWhere('stores.store_name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('access_page.employee_type', 'like', '%' . $searchTerm . '%')
                    ->orWhere('users.emp_id', 'like', '%' . $searchTerm . '%');
            });
        }
        $users = $usersQuery->orderByDesc('users.user_id')
            ->paginate(10)
            ->withQueryString();

        $users->transform(function ($item) {
            $item->status = $item->user_status == 'active';
            return $item;
        });
        // dd($users);

        return Inertia::render('Admin/Masterfile/UserSetup', [
            'data' => $users,
            'access_page' => $access_page,
            'store' => $Store,
            'search' => $request['searchData']
        ]);
    }
    public function updateUser(Request $request)
    {
        // dd($request->all());
        // Check if the data has no changes happen
        $checkUser = User::where('user_id', $request['user_id'])->first();
        if ($checkUser) {
            $CheckData =
                $checkUser->username === $request['username'] &&
                $checkUser->firstname === $request['firstname'] &&
                $checkUser->lastname === $request['lastname'] &&
                $checkUser->emp_id === $request['employee_id'] &&
                $checkUser->usertype === $request['usertype'] &&
                $checkUser->user_role === $request['user_role'] &&
                $checkUser->store_assigned === $request['store_assigned'] &&
                $checkUser->retail_group === $request['retail_group'] &&
                $checkUser->it_type === $request['it_type'] &&
                $checkUser->user_status === $request['status'];

            if ($CheckData) {
                return back()->with(
                    'error',
                    " {$request->username}'s data has no changes, please update first before submitting"
                );
            }
        }

        $validations = [
            'usertype' => 'required'
        ];
        if (in_array($request->usertype, [2, 3, 4, 5, 6, 9, 10, 11, 13, 14])) {
            $validations['user_role'] = 'required';
        }
        if (in_array($request->usertype, [7, 14])) {
            $validations['store_assigned'] = 'required';
        }
        if (in_array($request->usertype, [8])) {
            $validations['retail_group'] = 'required';
        }
        if (in_array($request->usertype, [12])) {
            $validations['it_type'] = 'required';
        }
        $request->validate($validations);

        User::where('user_id', $request->user_id)->update([
            'username' => $request->username,
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'emp_id' => $request->employee_id,
            'usertype' => $request->usertype,
            'usergroup' => null,
            'user_status' => 'active',
            'user_role' => in_array($request->usertype, [1]) ? 0 : $request->user_role,
            'login' => 'no',
            'promo_tag' => '0',
            'store_assigned' => in_array($request->usertype, [7, 14]) ? $request->store_assigned : 0,
            'date_created' => now(),
            'date_updated' => now(),
            'user_addby' => $request->user()->user_id,
            'retail_group' => in_array($request->usertype, [8]) ? $request->retail_group : null,
            'it_type' => in_array($request->usertype, [12]) ? $request->it_type : null
        ]);

        return back()->with(
            'success',
            'User updated successfully'
        );
    }


    public function users_save_user(Request $request)
    {
        $validations = [
            'usertype' => 'required'
        ];
        if (in_array($request->usertype, [2, 3, 4, 5, 6, 9, 10, 11, 13, 14])) {
            $validations['user_role'] = 'required';
        }
        if (in_array($request->usertype, [7, 14])) {
            $validations['store_assigned'] = 'required';
        }
        if (in_array($request->usertype, [8])) {
            $validations['retail_group'] = 'required';
        }
        if (in_array($request->usertype, [12])) {
            $validations['it_type'] = 'required';
        }
        $request->validate($validations);

        if (User::where('username', $request->username)->exists()) {
            return back()->with(
                'error',
                "{$request->username} username already exist, please try other username"
            );
        }

        $password = Hash::make('GC2015');

        User::create([
            'username' => $request->username,
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'emp_id' => $request->employee_id,
            'usertype' => $request->usertype,
            'password' => $password,
            'usergroup' => null,
            'user_status' => 'active',
            'user_role' => $request->user_role,
            'login' => 'no',
            'promo_tag' => '0',
            'store_assigned' => $request->store_assigned,
            'date_created' => now(),
            'user_addby' => $request->user()->user_id,
            'retail_group' => $request->retail_group,
            'it_type' => $request->it_type
        ]);

        return back()->with(
            'success',
            'User added successfully'
        );
    }
    public function usersResetPassword(Request $request)
    {
        // dd($request->all());
        $userPassword = User::where('user_id', $request['user_id'])->first();

        $defaultPassword = 'GC2015';
        $newPassword = Hash::make($defaultPassword);

        if (Hash::check($defaultPassword, $userPassword->password)) {
            return back()->with(
                'error',
                "{$userPassword->username}'s password already reset to default"
            );
        }

        $resetPassword = User::where('user_id', $request['user_id'])->update([
            'password' => $newPassword
        ]);

        if ($resetPassword) {
            return back()->with(
                'success',
                'Password reset successfully'
            );
        }
        return back()->with(
            'error',
            'ERROR'
        );
    }
    public function updateStatus(Request $request)
    {
        // dd($request->toArray());
        $user = User::where('user_id', $request->id)->first();

        if ($user) {
            User::where('user_id', $request->id)
                ->update([
                    'user_status' => $user['user_status'] == 'active' ? 'inactive' : 'active'
                ]);

            return back()->with([
                'type' => 'success',
                'msg' => 'SUCCESS',
                'description' => 'Status updated successfully'
            ]);
        }
    }
    public function issueReceipt(Request $request)
    {
        // dd($request->store_id);
        $receipt = Store::where('store_id', $request->store_id)->first();

        if ($receipt) {
            Store::where('store_id', $receipt->store_id)->update([
                'issuereceipt' => $receipt['issuereceipt'] == 'yes' ? 'no' : 'yes'
            ]);

            return back()->with(
                'success',
                'SUCCESS'
            );
        }
        return back()->with([
            'error',
            'FAILED TO UPDATE'

        ]);
    }
    public function eodReports(Request $request)
    {
        return inertia('Admin/EodReports', [
            'record' => $this->adminservices->getEodDateRange($request)
        ]);
    }
    public function storeSetup(Request $request)
    {

        $StoreStaff = StoreStaff::select();


        $store = Store::all();
        $searchTerm = $request->input('data', '');

        $password = User::get();

        $selectEntries = $request->input('value', 10);
        $searchTerm = $request->input('data', '');

        // $data = StoreStaff::query();

        $data = StoreStaff::select(
            'store_staff.ss_id',
            'store_staff.ss_idnumber',
            'store_staff.ss_username',
            'store_staff.ss_usertype',
            'store_staff.ss_firstname',
            'store_staff.ss_lastname',
            'store_staff.ss_store',
            'store_staff.ss_password',
            'stores.store_id',
            'stores.store_name',
            'store_staff.ss_date_created',
            'store_staff.ss_status',
            'store_staff.ss_password'
        )

            ->leftJoin('stores', 'store_staff.ss_store', '=', 'stores.store_id');

        if ($searchTerm) {
            $data = $data->where(function ($query) use ($searchTerm) {
                $query->where('store_staff.ss_idnumber', 'like', '%' . $searchTerm . '%')
                    ->orWhere('store_staff.ss_username', 'like', '%' . $searchTerm . '%')
                    ->orWhere('store_staff.ss_usertype', 'like', '%' . $searchTerm . '%')
                    ->orWhere('store_staff.ss_firstname', 'like', '%' . $searchTerm . '%')
                    ->orWhere('store_staff.ss_lastname', 'like', '%' . $searchTerm . '%')
                    ->orWhere('stores.store_name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('store_staff.ss_password', 'like', '%' . $searchTerm . '%');
            });
        }

        $data = $data->orderByDesc('ss_id')
            ->paginate($selectEntries)
            ->withQueryString();

        return Inertia::render('Admin/Masterfile/SetupStoreStaff', [
            'data' => $data,
            'search' => $request->data,
            'store' => $store,
            'password' => $password,
            'StoreStaff' => $StoreStaff,
            'value' => $request->value

        ]);
    }

    public function saveUser(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'ss_username' => 'required|max:50',
            'ss_firstname' => 'required',
            'ss_lastname' => 'required',
            'ss_idnumber' => 'required|integer',
            'ss_password' => 'required',
            'ss_store' => 'required',
            'ss_usertype' => 'required',
        ]);

        $storeStaff = StoreStaff::where('ss_username', $request->ss_username)->first();
        if ($storeStaff) {
            return back()->with(
                'error',
                'OPPS'
            );
        }

        $pass = Hash::make($request->ss_password);

        $isSuccessfull = StoreStaff::create([
            'ss_username' => $request->ss_username,
            'ss_firstname' => $request->ss_firstname,
            'ss_lastname' => $request->ss_lastname,
            'ss_idnumber' => $request->ss_idnumber,
            'ss_password' => $pass,
            'ss_usertype' => $request->ss_usertype,
            'ss_status' => 'active',
            'ss_store' => $request->ss_store,
            'ss_manager_key' => $pass,
            'ss_date_created' => now(),
            'ss_date_modified' => now(),
            'ss_by' => $request->user()->user_id
        ]);

        if ($isSuccessfull->wasRecentlyCreated) {
            return back()->with(
                'success',
                'SUCCESS'
            );
        }
        return back()->with(
            'error',
            'FAILED TO ADD'
        );
    }
    public function customerSetup(Request $request)
    {
        // dd($request->tabs);
        $institutional = InstitutCustomer::get();
        $special = SpecialExternalCustomer::get();
        $store = Store::get();
        $data = Customer::get();
        $searchTerm = $request->input('data', '');
        $activeTab = $request->input('tabs', 'store_customer');
        // dd($activeTab);

        $data = DB::table('customers')
            ->select(
                'cus_id',
                'cus_store_register',
                'cus_register_at',
                'cus_register_by',
                'cus_fname',
                'cus_lname',
                'cus_mname',
                'cus_namext',
                'stores.store_id',
                'stores.store_name',
                'institut_customer.ins_id',
                'institut_customer.ins_name as ins_name',
                'institut_customer.ins_status as ins_status',
                'institut_customer.ins_custype as ins_custype',
                'institut_customer.ins_gctype as ins_gctype',
                'institut_customer.ins_date_created as ins_date_created',
                'institut_customer.ins_by as ins_by',
                'special_external_customer.spcus_id',
                'special_external_customer.spcus_companyname as spcus_companyname',
                'special_external_customer.spcus_acctname as spcus_acctname',
                'special_external_customer.spcus_address as spcus_address',
                'special_external_customer.spcus_cperson as spcus_cperson',
                'special_external_customer.spcus_cnumber as spcus_cnumber',
                'special_external_customer.spcus_type as spcus_type',
                'users.user_id',
                'users.firstname as firstname',
                'users.lastname as lastname',
                User::raw("CONCAT(users.firstname, ' ', users.lastname) as fullname")

            )
            ->whereAny([
                'cus_id',
                'cus_store_register',
                'cus_register_at',
                'cus_register_by',
                'cus_fname',
                'cus_lname',
                'cus_mname',
                'cus_namext',
                'stores.store_id',
                'stores.store_name',
                'institut_customer.ins_id',
                'institut_customer.ins_name',
                'institut_customer.ins_status',
                'institut_customer.ins_custype',
                'institut_customer.ins_gctype',
                'institut_customer.ins_date_created',
                'institut_customer.ins_by',
                'special_external_customer.spcus_id',
                'special_external_customer.spcus_companyname',
                'special_external_customer.spcus_acctname',
                'special_external_customer.spcus_address',
                'special_external_customer.spcus_cperson',
                'special_external_customer.spcus_cnumber',
                'special_external_customer.spcus_type',
                'users.user_id',
                'users.firstname',
                'users.lastname',
            ], 'like', '%' . $searchTerm . '%')

            ->leftJoin('users', 'cus_register_by', '=', 'users.user_id')
            ->leftJoin('institut_customer', 'cus_id', '=', 'institut_customer.ins_id')
            ->leftJoin('special_external_customer', 'cus_id', '=', 'special_external_customer.spcus_id')
            ->leftJoin('stores', 'cus_store_register', '=', 'stores.store_id');

        if ($activeTab === 'store_customer') {
            $data = $data->whereNotNull('cus_id')
                ->orderBy('cus_id', 'asc')
                ->paginate(10);
        } elseif ($activeTab === 'institutional_customer') {
            $data = $data->whereNotNull('institut_customer.ins_id')
                ->orderBy('institut_customer.ins_id', 'ASC')
                ->paginate(10);
        } elseif ($activeTab === 'special_customer') {
            $data = $data->whereNotNull('special_external_customer.spcus_id')
                ->orderBy('special_external_customer.spcus_id', 'ASC')
                ->paginate(10);
        }

        return inertia('Admin/Masterfile/CustomerSetup', [
            'data' => $data,
            'search' => $request->data,
            'value' => $request->value,
            'institutional' => $institutional,
            'special' => $special,
            'store' => $store,

        ]);
    }
    public function updateCustomerStoreRegister(Request $request)
    {
        // dd($request->toArray());
        $request->validate([
            'cus_fname' => 'required',
            'cus_lname' => 'required',
            'cus_store_register' => 'required'
        ]);
        $validation = Customer::findOrFail($request->cus_id);
        if (
            $validation->cus_fname == $request->cus_fname
            && $validation->cus_lname == $request->cus_lname
            && $validation->cus_store_register == $request->cus_store_register
        ) {
            return back()->with('error', 'WARNING');
        }


        $onSuccess = Customer::where('cus_id', $request->cus_id)->update([
            'cus_fname' => $request->cus_fname,
            'cus_lname' => $request->cus_lname,
            'cus_store_register' => $request->cus_store_register

        ]);
        if ($onSuccess) {
            return back()->with(
                'success',
                'SUCCESS'
            );
        }
        return back()->with('error', 'WARNING');
    }
    public function setupStore(Request $request)
    {

        // dd($request->toArray());

        $searchTerm = $request->input('data', '');
        $selectEntries = $request->input('value', 10);

        $data = store::query();

        if ($searchTerm) {
            $data = $data->where(function ($query) use ($searchTerm) {
                $query->where('store_name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('store_code', 'like', '%' . $searchTerm . '%')
                    ->orWhere('company_code', 'like', '%' . $searchTerm . '%')
                    ->orWhere('default_password', 'like', '%' . $searchTerm . '%');
            });
        }
        $data = $data->orderByDesc('store_id')
            ->paginate($selectEntries)
            ->withQueryString();

        // $data = $data->paginate(10);
        $data->transform(function ($item) {
            $item->status = $item->issuereceipt == 'yes';
            return $item;
        });

        return inertia('Admin/Masterfile/SetupStore', [
            'data' => $data,
            'search' => $request->data,
            'value' => $request->value
        ]);
    }
    public function saveStore(Request $request)
    {

        // dd($request->toArray());
        $request->validate([
            'store_name' => 'required|string|max:50',
            'store_code' => 'required|integer',
            'company_code' => 'required|integer',
            'default_password' => 'required'
        ]);

        $isSuccessful = Store::create([
            'store_name' => $request->store_name,
            'store_code' => $request->store_code,
            'company_code' => $request->company_code,
            'default_password' => $request->default_password,
            'store_status' => 'active',
            'issuereceipt' => '',
            'store_bng' => '',
            'has_local' => '1',
            'store_textfile_ip' => '172.16.161.205\CFS_Txt\GiftCheck',
            'store_initial' => '',

        ]);

        if ($isSuccessful) {
            return back()->with(
                'success',
                'SUCCESS'
            );
        }
        return back()->with(
            'error',
            'FAILED TO ADD'
        );
    }

    public function creditCardSetup(Request $request)
    {
        $searchTerm = $request->input('data', '');
        $dataQuery = creditcard::query();

        if ($searchTerm) {
            $dataQuery->where(function ($query) use ($searchTerm) {
                $query->where('ccard_name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('ccard_status', 'like', '%' . $searchTerm . '%')
                    ->orWhere('ccard_created', 'like', '%' . $searchTerm . '%')
                    ->orWhere('ccard_by', 'like', '%' . $searchTerm . '%');
            });
        }

        $data = $dataQuery->orderByDesc('ccard_id')
            ->paginate(10)
            ->through(function ($item) {
                $item['ccard_created_formatted'] = Carbon::parse($item['ccard_created'])->format('Y-m-d H:i:s');
                return $item;
            })
            ->withQueryString();

        return inertia('Admin/Masterfile/SetupCreditCard', [
            'data' => $data,
            'search' => $searchTerm,
        ]);
    }


    public function saveCreditCard(Request $request)
    {
        // dd($request->toArray());
        $request->validate([
            'ccard_name' => 'required|string|max:30'
        ]);

        $isSuccessful = creditcard::create([
            'ccard_name' => $request->ccard_name,
            'ccard_status' => 1,
            'ccard_created' => now(),
            'ccard_by' => $request->user()->user_id,
        ]);

        if ($isSuccessful) {
            return back()->with(
                'success',
                'Credit Card added successfully'
            );
        }
        return back()->with(
            'error',
            'Failed to add credit card'
        );
    }

    public function denominationSetup(Request $request)
    {
        $data = Denomination::get();
        $searchTerm = $request->input('data', '');
        $data = DB::table('denomination')
            ->select(
                'denom_id',
                'denom_code',
                'denomination',
                'denom_fad_item_number',
                'denom_barcode_start',
                'denom_type',
                'denom_status',
                'denom_createdby',
            )
            ->whereAny([
                'denom_id',
                'denom_code',
                'denomination',
                'denom_fad_item_number',
                'denom_barcode_start',
                'denom_type',
                'denom_status',
                'denom_createdby'
            ], 'like', '%' . $searchTerm . '%')
            ->orderBy('denom_id', 'ASC')
            ->paginate(10);

        return inertia('Admin/Masterfile/SetupDenomination', [
            'data' => $data,
            'search' => $searchTerm,
            'value' => $request->value
        ]);
    }


    public function saveDenomination(Request $request)
    {
        // dd($request->toArray());
        $request->validate([
            'denomination' => 'required|integer',
            'denom_barcode_start' => 'required|integer',
        ]);

        $denom_code = Denomination::max('denom_code');
        $newDenomCode = $denom_code ? $denom_code + 1 : 1;

        $denom_fad_item_number = Denomination::max('denom_fad_item_number');
        $newDenomFadItemNumber = $denom_fad_item_number ? $denom_fad_item_number + 1 : 1;

        $newDenom = Denomination::where('denomination', $request->denomination)->first();
        if ($newDenom) {
            return back()->with('error', 'OPPS');
        }
        $isSuccessful = Denomination::create([
            'denomination' => $request->denomination,
            'denom_barcode_start' => $request->denom_barcode_start,
            'denom_dateupdated' => now(),
            'denom_code' => $newDenomCode,
            'denom_fad_item_number' => $newDenomFadItemNumber,
            'denom_type' => 'RSGC',
            'denom_status' => 'active',
            'denom_createdby' => $request->user()->user_id,
            'denom_datecreated' => now(),

        ]);
        if ($isSuccessful) {
            return back()->with(
                'success',
                'Denomination added successfully'
            );
        }
        return back()->with(
            'error',
            'Failed to add denomination'
        );
    }

    public function UpdateDenomination(Request $request)
    {
        // dd($request->toArray());

        $request->validate([
            'denomination' => 'required',
            'denom_barcode_start' => 'required|regex:/^[0-9]{0,13}$/',
            'denom_fad_item_number' => 'required'
        ], [
            'denom_barcode_start.regex' => 'denom_barcode_start field must be atleast 0 to 13 digits'
        ]);

        $denomination = Denomination::findOrFail($request->denom_id);
        if (
            $denomination->denomination == $request->denomination &&
            $denomination->denom_barcode_start == $request->denom_barcode_start &&
            $denomination->denom_fad_item_number == $request->denom_fad_item_number
        ) {
            return back()->with('error', 'No changes happen, update first before submitting');
        }


        $isSuccessfull = Denomination::where('denom_id', $request->denom_id)->update([
            'denomination' => $request->denomination,
            'denom_barcode_start' => $request->denom_barcode_start,
            'denom_fad_item_number' => $request->denom_fad_item_number
        ]);
        if ($isSuccessfull) {
            return back()->with(
                'success',
                'Denomination updated successfully'
            );
        }
        return back()->with(
            'error',
            'FAILED TO UPDATE'
        );
    }

    public function updateStoreStaffSetup(Request $request)
    {
        // dd($request->toArray());

        $request->validate([
            'ss_idnumber' => 'required|integer',
            'ss_username' => 'required',
            'ss_usertype' => 'required',
            'ss_firstname' => 'required',
            'ss_lastname' => 'required',
            'ss_store' => 'required',
        ]);
        $isSuccessfull = StoreStaff::where('ss_id', $request->ss_id)->update([
            'ss_idnumber' => $request->ss_idnumber,
            'ss_username' => $request->ss_username,
            'ss_usertype' => $request->ss_usertype,
            'ss_firstname' => $request->ss_firstname,
            'ss_lastname' => $request->ss_lastname,
            'ss_store' => $request->ss_store
        ]);
        if ($isSuccessfull) {
            return back()->with(
                'success',
                'User updated successfully'
            );
        }
        return back()->with(
            'error',
            'No changes, update first before submitting'
        );
    }
    public function updateStoreStaffPassword(Request $request)
    {
        // dd($request->ss_id);
        $defaultPassword = 'GC2015';

        $users = StoreStaff::where('ss_id', $request->ss_id)->first();
        if (Hash::check($defaultPassword, $users->ss_password)) {
            return back()->with(
                'error',
                'This username password already reset to default'
            );
        }
        $newPassword = Hash::make($defaultPassword);

        $onSuccess = StoreStaff::where('ss_id', $request->ss_id)->update([
            'ss_password' => $newPassword
        ]);
        if ($onSuccess) {
            return back()->with(
                'success',
                'Password reset successfully'
            );
        }
        return back()->with(
            'error',
            'FAILED TO UPDATE'
        );
    }
    public function revolving_fund(Request $request)
    {

        $data = Store::get();
        $searchTerm = $request->input('data', '');
        $selectEntries = $request->input('value', 10);
        $data = Store::query();

        if ($searchTerm) {
            $data->where(function ($query) use ($searchTerm) {
                $query->where('store_code', 'like', '%' . $searchTerm . '%')
                    ->orWhere('store_name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('store_status', 'like', '%' . $searchTerm . '%');
            });
        }
        $data = $data->orderByDesc('store_id')
            ->paginate($selectEntries)
            ->withQueryString();

        return Inertia::render('Admin/Masterfile/RevolvingFund', [
            'data' => $data,
            'search' => $request->data,
            'value' => $request->value
        ]);
    }

    public function tagHennan(Request $request)
    {
        // dd($request->all());
        $fullname = DB::table('special_gc_henanns')
            ->select('fullname', 'hennan_id')
            ->get();
        $query = DB::table('special_external_gcrequest_emp_assign')
            ->join('special_gc_henanns', 'special_gc_henanns.hennan_id', '=', 'special_external_gcrequest_emp_assign.tag')
            ->select('special_external_gcrequest_emp_assign.spexgcemp_barcode', 'special_external_gcrequest_emp_assign.tag', 'special_gc_henanns.fullname', 'special_gc_henanns.hennan_id')
            ->whereAny([
                'spexgcemp_barcode',
                'fullname'
            ], 'like', '%' . $request['searchvalue'] . '%')
            ->orderBy('special_external_gcrequest_emp_assign.spexgcemp_id', 'DESC')
            ->paginate(10);
        return Inertia::render('Admin/Masterfile/TagHennan', [
            'data' => $query,
            'fullname' => $fullname,
            'search' => $request['searchvalue']
        ]);
    }

    public function updateTagHennan(Request $request)
    {
        // dd($request->spexgcemp_barcode);

        $updateTag = DB::table('special_gc_henanns')->where('hennan_id', $request->hennan_id)->update([
            'tagged' => '1',

        ]);
        $update = DB::table('special_external_gcrequest_emp_assign')->where('spexgcemp_barcode', $request->spexgcemp_barcode)->update([
            'tag' => $request->hennan_id
        ]);

        // tagged value back to 0 if hennan_id is not found in the tag column
        $updateTagBack = DB::table('special_gc_henanns')
            ->leftJoin('special_external_gcrequest_emp_assign', 'special_gc_henanns.hennan_id', '=', 'special_external_gcrequest_emp_assign.tag')
            ->whereNull('special_external_gcrequest_emp_assign.tag')
            ->update([
                'tagged' => '0',
            ]);


        if ($updateTag && $update && $updateTagBack) {

            return back()->with(
                'success',
                'SUCCESS'
            );
        } else {
            return back()->with(
                'warning',
                'FAILED TO UPDATE'
            );
        }
    }

    public function blockBarcode(Request $request)
    {
        // dd($request->all());

        $data = DB::table('blocked_barcodes')
            ->select('id', 'barcode', 'status', 'created_at', 'updated_at')
            ->whereAny([
                'barcode',
                'status',
                'created_at'
            ], 'like', '%' . $request['searchBarcode'] . '%')
            ->orderBy('blocked_barcodes.id', 'DESC')
            ->paginate(10);

        return Inertia::render('Admin/Masterfile/BlockBarcode', [
            'data' => $data,
            'searchValue' => $request['searchBarcode']
        ]);
    }

    public function addBlockedBarcode(Request $request)
    {
        $request->validate([
            'barcode' => 'required'
        ]);
        // dd($request->all());
        $checkBarcode = DB::table('blocked_barcodes')->where('barcode', $request['barcode'])->exists();

        if ($checkBarcode) {
            return back()->with(
                'error',
                'This barcode is already in the blockedlist, please try other barcode'
            );
        }
        $date = now();
        $onSuccess = DB::table('blocked_barcodes')->insert([
            'barcode' => $request['barcode'],
            'status' => 'blocked',
            'created_at' => $date->format('Y-m-d h:i:s'),
            'updated_at' => $date->format('Y-m-d h:i:s')

        ]);
        if ($onSuccess) {
            return back()->with(
                'success',
                'Barcode blocked successfully'
            );
        }
    }

    public function unblockedBarcode(Request $request)
    {
        $unblockedBarcode = DB::table('blocked_barcodes')->where('id', $request['barcode_id'])->update([
            'status' => 'free',
        ]);

        if ($unblockedBarcode) {
            return back()->with(
                'success',
                'Barcode is unblocked, free and ready to use'
            );
        }
    }

    public function blockedAgain(Request $request)
    {
        // dd($request->all());
        $unblockedBarcode = DB::table('blocked_barcodes')->where('id', $request['barcode_id'])->update([
            'status' => 'blocked',
        ]);

        if ($unblockedBarcode) {
            return back()->with(
                'success',
                'Barcode is blocked successfully',
            );
        } else {
            return back()->with(
                'success',
                'Failed to blocked the barcode',
            );
        }
    }


    public function updateRevolvingFund(Request $request)
    {
        // dd($request->store_id);
        $request->validate([
            'r_fund' => 'required|integer',
            'store_status' => 'required'
        ]);
        $revolvingFund = Store::findOrFail($request->store_id);

        if (
            $revolvingFund->r_fund == $request->r_fund &&
            $revolvingFund->store_status == $request->store_status
        ) {
            return back()->with('error', 'FAILED TO UPDATE');
        }

        $onsuccess = Store::where('store_id', $request->store_id)->update([
            'r_fund' => $request->r_fund,
            'store_status' => $request->store_status
        ]);
        if ($onsuccess) {
            return back()->with(
                'success',
                'SUCCESS'
            );
        }
        return back()->with(
            'error',
            'FAILED TO UPDATE'
        );
    }
    public function updateInstituteCustomer(Request $request)
    {
        $request->validate([
            'ins_name' => 'required',
            'ins_gctype' => [
                'sometimes',
                Rule::requiredIf(function () use ($request) {
                    return $request->input('ins_custype') === 'internal' && $request->input('ins_gctype') === 0;
                }),
            ],
            'ins_custype' => 'required',
            'ins_status' => 'required'
        ]);

        $institute = InstitutCustomer::findOrFail($request->ins_id);
        if (
            $institute->ins_name == $request->ins_name
            && $institute->ins_gctype == $request->ins_gctype
            && $institute->ins_custype == $request->ins_custype
            && $institute->ins_status == $request->ins_status
        ) {
            return back()->with('error', 'WARNING');
        }

        $updateInstitute = [
            'ins_name' => $request->ins_name,
            'ins_custype' => $request->ins_custype,
            'ins_status' => $request->ins_status,
            'ins_date_updated' => now(),
        ];
        if ($request->ins_custype === 'external') {
            $updateInstitute['ins_gctype'] = 0;
        } else {
            $updateInstitute['ins_gctype'] = $request->ins_gctype;
        }

        $onSuccess = InstitutCustomer::where('ins_id', $request->ins_id)->update($updateInstitute);
        if ($onSuccess) {
            return back()->with(
                'success',
                'SUCCESS'
            );
        }
        return back()->with(
            'error',
            'ERROR'
        );
    }
    public function updateSpecialCustomer(Request $request)
    {
        $request->validate([
            'spcus_companyname' => 'required',
            'spcus_acctname' => 'required'
        ]);
        // dd($request->toArray());
        $validation = SpecialExternalCustomer::findOrFail($request->spcus_id);
        if (
            $validation->spcus_companyname == $request->spcus_companyname
            && $validation->spcus_acctname == $request->spcus_acctname
            && $validation->spcus_address == $request->spcus_address
            && $validation->spcus_cperson == $request->spcus_cperson
            && $validation->spcus_cnumber == $request->spcus_cnumber
            && $validation->spcus_type == $request->spcus_type
        ) {
            return back()->with(
                'error',
                'WARNING'
            );
        }
        $onSuccess = SpecialExternalCustomer::where('spcus_id', $request->spcus_id)->update([
            'spcus_companyname' => $request->spcus_companyname,
            'spcus_acctname' => $request->spcus_acctname,
            'spcus_address' => $request->spcus_address,
            'spcus_cperson' => $request->spcus_cperson,
            'spcus_cnumber' => $request->spcus_cnumber,
            'spcus_type' => $request->spcus_type
        ]);
        if ($onSuccess) {
            return back()->with(
                'success',
                'SUCCESS'
            );
        }
        return back()->with(
            'error',
            'ERROR'
        );
    }

    public function submitPurchaseOrdersToIad(Request $request)
    {
        return $this->adminservices->submitOrderPurchase($request);
    }
    public function setupPurchaseOrders($name)
    {

        $data = $this->adminservices->getPoDetailsTextfiles($name);

        return inertia('Admin/SetupPurchaseOrders', [
            'record' => $data,
            'denom' => $this->adminservices->getDenomination($data->denom),
            'title' => $name,
        ]);
    }
}
