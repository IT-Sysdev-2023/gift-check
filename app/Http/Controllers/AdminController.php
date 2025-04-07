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
use App\Models\AdminImages;
use App\Models\SpecialExternalGcrequestEmpAssign;
use App\Services\Treasury\Transactions\SpecialGcPaymentService;
use BcMath\Number;
use Illuminate\Support\Facades\DB;
use Symfony\Contracts\Service\Attribute\Required;
use Carbon\Carbon;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    public function __construct(public AdminServices $adminservices, public DBTransaction $dBTransaction)
    {
    }
    public function submitNewUsername(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'username' => 'required',
            'password.password' => 'required'
        ], [
            'password.password.required' => 'Password field is required'
        ]);

        $password = User::findOrFail($request->id);
        if (!Hash::check($request->password['password'], $password->password)) {
            return response()->json([
                'error' => true,
                'message' => 'Confirmation password is incorrect, please try again'
            ]);
        }

        if ($request->username == $password->username) {
            return response()->json([
                'error' => true,
                'message' => 'Username is not change, please update first'
            ]);
        }

        $password->update([
            'username' => $request->username
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Username updated successfully'
        ]);

    }

    public function submitNewPassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|same:confirm_password',
            'confirm_password' => 'required|same:new_password'
        ]);

        $oldPassword = User::findOrFail($request->id);

        if (!Hash::check($request->old_password, $oldPassword->password)) {
            return response()->json([
                'error' => true,
                'message' => 'Old password is incorrect, please try again.'
            ]);
        }
        if (Hash::check($request->new_password, $oldPassword->password)) {
            return response()->json([
                'error' => true,
                'message' => 'New password is the same as the old password, please try again.'
            ]);
        }
        $oldPassword->update([
            'password' => Hash::make($request->new_password)
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully'
        ]);
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
        $storeQuery = DB::table('stores')
            ->leftJoin('transaction_stores', 'stores.store_id', '=', 'transaction_stores.trans_store')
            ->leftJoin('transaction_sales', 'transaction_sales.sales_transaction_id', '=', 'transaction_stores.trans_sid')
            ->leftJoin('gc', 'gc.barcode_no', '=', 'transaction_sales.sales_barcode')
            ->leftJoin('denomination', 'denomination.denom_id', '=', 'gc.denom_id')
            ->select(
                'stores.store_name',
                DB::raw("IFNULL(COUNT(transaction_sales.sales_barcode), 0) as gc_count")
            )
            ->whereYear('transaction_stores.trans_datetime', $currentYear)
            ->whereMonth('transaction_stores.trans_datetime', $currentMonth)
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

        $storesQuery = DB::query()->fromSub($institutionQuery, 'institutionQuery')
            ->unionAll(
                DB::table('approved_gcrequest')
                    ->join('store_gcrequest', 'store_gcrequest.sgc_id', '=', 'approved_gcrequest.agcr_request_id')
                    ->join('stores', 'stores.store_id', '=', 'store_gcrequest.sgc_store')
                    ->select(
                        DB::raw("MONTH(approved_gcrequest.agcr_approved_at) as month"),
                        DB::raw("COUNT(*) as total_count")
                    )
                    ->whereYear('approved_gcrequest.agcr_approved_at', $currentYear)
                    ->groupBy(DB::raw("MONTH(approved_gcrequest.agcr_approved_at)"))
            );

        $specialQuery = DB::query()->fromSub($storesQuery, 'storesQuery')
            ->unionAll(
                DB::table('special_external_gcrequest')
                    ->join('special_external_customer', 'special_external_customer.spcus_id', '=', 'special_external_gcrequest.spexgc_company')
                    ->select(
                        DB::raw("MONTH(special_external_gcrequest.spexgc_datereq) as month"),
                        DB::raw("COUNT(*) as total_count")
                    )
                    ->whereYear('special_external_gcrequest.spexgc_datereq', $currentYear)
                    ->groupBy(DB::raw("MONTH(special_external_gcrequest.spexgc_datereq)"))
            );

        // Perform final aggregation of Special GC
        $specialGcData = collect($specialGcQuery);

        // Perform final aggregation of Regular GC
        $storesData = collect(DB::select($storesQuery->toSql(), $storesQuery->getBindings()));
        $institutionData = collect(DB::select($institutionQuery->toSql(), $institutionQuery->getBindings()));
        $specialData = collect(DB::select($specialQuery->toSql(), $specialQuery->getBindings()));

        //Merge all the data of Regular Gc
        $mergedData = collect($storesData)->merge($institutionData)->merge($specialData);

        // Perform final aggregation in all regular gc data
        $finalQuery = $mergedData->groupBy('month')->map(function ($group) {
            return [
                'month' => $group->first()->month,
                'total_transactions' => $group->sum('total_count'),
            ];
        })->values();

        return inertia('Admin/AdminDashboard', [
            'users' => $users, //gettin all users data
            'storeQuery' => $storeQuery, //per store query
            'specialGcData' => $specialGcData, // special gc query
            'regularGcData' => $finalQuery // merge query of all regular gc
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

        $accessName = [
            '1' => 'Administrator',
            '2' => 'Corporate Treasury',
            '3' => 'Corporate Finance',
            '4' => 'Corporate FAD',
            '5' => 'General Manager',
            '6' => 'Corporate Marketing',
            '7' => 'Retail Store',
            '8' => 'Retail Group',
            '9' => 'Corporate Accounting',
            '10' => 'Internal Audit',
            '11' => 'Finance Officer',
            '12' => 'IT Personnel ',
            '13' => 'CFS / Store Accounting',
            '14' => '',

        ];
        $access_page = AccessPage::get();
        $formattedUsertype = $access_page->filter(function ($item) {
            return $item->access_no != 14;
        })->transform(function ($item) use ($accessName) {
            $item->usertype = $accessName[$item->access_no] ?? null;
            return $item;
        });

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
        $userRole = [
            '1' => 'Dept. User',
            '0' => 'Dept. Manager',
            '2' => 'Releasing Personel'
        ];
        $retailGroup = [
            '1' => 'Group 1',
            '2' => 'Group 2'
        ];
        $itType = [
            '1' => 'Corporate IT',
            '2' => 'Store IT'
        ];


        $users->transform(function ($item) use ($userRole, $retailGroup, $itType, $accessName) {
            $item->status = $item->user_status == 'active';
            $item->userRole = $userRole[$item->user_role] ?? '';
            $item->userType = AccessPage::where('access_no', $item->usertype)->value('title') ?? '';
            $item->storeAssigned = Store::where('store_id', $item->store_assigned)->value('store_name') ?? '';
            $item->retailGroup = $retailGroup[$item->retail_group] ?? '';
            $item->itType = $itType[$item->it_type] ?? '';
            $item->userGroup = $accessName[$item->usertype] ?? '';

            return $item;
        });

        return Inertia::render('Admin/Masterfile/UserSetup', [
            'data' => $users,
            'access_page' => $formattedUsertype,
            'store' => $Store,
            'search' => $request['searchData']
        ]);
    }
    public function updateUser(Request $request)
    {
        // this is the usertype mapping guide
        // '1' => 'admin',
        // '2' => 'treasury',
        // '3' => 'finance',
        // '4' => 'custodian',
        // '6' => 'marketing',
        // '7' => 'retail',
        // '8' => 'retailgroup',
        // '9' => 'accounting',
        // '11' => 'storeaccounting'
        // '10' => 'iad',
        // '12' => 'eod',
        // '13' => 'storeaccounting',


        $userType = [
            'Administrator' => 1,
            'Corporate Treasury' => 2,
            'Corporate Finance' => 3,
            'Corporate FAD' => 4,
            'General Manager' => 5,
            'Corporate Marketing' => 6,
            'Retail Store' => 7,
            'Retail Group' => 8,
            'Corporate Accounting' => 9,
            'Internal Audit' => 10,
            'Finance Officer' => 11,
            'IT Personnel' => 12,
            'CFS' => 13,
        ];

        $userRole = [
            'Dept. User' => 1,
            'Dept. Manager' => 0,
            'Releasing Personel' => 2
        ];

        $storeAssigned = [
            'Alturas Mall' => 1,
            'Alturas Talibon' => 2,
            'Island City Mall' => 3,
            'Plaza Marcela' => 4,
            'Alturas Tubigon' => 5,
            'Colonade Colon' => 6,
            'Colonade Mandaue' => 7,
            'Alta Citta' => 8,
            'Farmers Market' => 9,
            'Ubay Distribution Center' => 10,
            'Screenville' => 11,
            'Asc Tech' => 12
        ];

        $retailGroup = [
            'Group 1' => 1,
            'Group 2' => 2
        ];

        $itType = [
            'Corporate IT' => 1,
            'Store IT' => 2
        ];
        $usertypeInt = $userType[$request->usertype] ?? null;

        $validation = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,user_id',
            'usertype' => 'required|string|in:' . implode(',', array_keys($userType)),
            'username' => 'required|string|max:255',
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'employee_id' => 'required|string|max:50',

            'user_role' => [
                function ($attribute, $value, $fail) use ($usertypeInt, $userRole) {
                    if ($usertypeInt !== 1 && (!isset($userRole[$value]) || !array_key_exists($value, $userRole))) {
                        $fail("The selected {$attribute} is invalid.");
                    }
                }
            ],

            'store_assigned' => [
                function ($attribute, $value, $fail) use ($usertypeInt, $storeAssigned, $itType) {
                    $shouldValidate = ($usertypeInt == 7 || ($itType[request()->input('it_type')] ?? null) == 2);

                    if ($shouldValidate && $value !== null) {
                        if (empty($storeAssigned[$value])) {
                            $fail("The selected {$attribute} is invalid.");
                        }
                    }
                }
            ],

            'retail_group' => [
                function ($attribute, $value, $fail) use ($usertypeInt, $retailGroup) {
                    if ($usertypeInt === 8 && (!isset($retailGroup[$value]) || !array_key_exists($value, $retailGroup))) {
                        $fail("The selected {$attribute} is invalid.");
                    }
                }
            ],

            'it_type' => [
                function ($attribute, $value, $fail) use ($usertypeInt, $itType) {
                    if ($usertypeInt === 12 && (!isset($itType[$value]) || !array_key_exists($value, $itType))) {
                        $fail("The selected {$attribute} is invalid.");
                    }
                }
            ]
        ]);

        if ($validation->fails()) {
            return back()->with(
                'error',
                'Please fill all the required fields'
            );
        }

        // Convert request strings to mapped integers
        $userRoleInt = isset($request->user_role) ? ($userRole[$request->user_role] ?? null) : null;
        $storeAssignedInt = isset($request->store_assigned) ? ($storeAssigned[$request->store_assigned] ?? null) : null;
        $retailGroupInt = isset($request->retail_group) ? ($retailGroup[$request->retail_group] ?? null) : null;
        $itTypeInt = isset($request->it_type) ? ($itType[$request->it_type] ?? null) : null;

        // Update user data
        User::where('user_id', $request->user_id)->update([
            'username' => $request->username,
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'emp_id' => $request->employee_id,
            'usertype' => $usertypeInt,
            'usergroup' => null,
            'user_status' => 'active',
            'user_role' => $userRoleInt,
            'login' => 'no',
            'promo_tag' => '0',
            'store_assigned' => (in_array($usertypeInt, [7]) || $itTypeInt == 2) ? $storeAssignedInt : null,
            'date_updated' => now(),
            'user_addby' => $request->user()->user_id,
            'retail_group' => $usertypeInt === 8 ? $retailGroupInt : null,
            'it_type' => $usertypeInt === 12 ? $itTypeInt : null
        ]);

        return back()->with('success', 'User updated successfully');
    }


    public function users_save_user(Request $request)
    {
        // dd($request->all());
        $validations = [
            'usertype' => 'required',
            'firstname' => 'required',
            'lastname' => 'required',
            'username' => 'required',
        ];
        if (in_array($request->usertype, [2, 3, 4, 5, 6, 9, 10, 11, 13])) {
            $validations['user_role'] = 'required';
        }
        if (in_array($request->usertype, [7]) || $request->it_type == 2) {
            $validations['store_assigned'] = 'required';
        }
        if (in_array($request->usertype, [8])) {
            $validations['retail_group'] = 'required';
        }
        if (in_array($request->usertype, [12])) {
            $validations['it_type'] = 'required';
        }
        // $request->validate($validations);
        $validation = Validator::make($request->all(), $validations);
        if ($validation->fails()) {
            return back()->with(
                'error',
                'Please fill all required fields'
            );
        }
        if (User::where('username', $request->username)->exists()) {
            return back()->with(
                'error',
                "{$request->username} username already exist, please try other username"
            );
        }


        $password = Hash::make('GC2025');
        User::create([
            'username' => $request->username,
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'emp_id' => $request->employee_id,
            'usertype' => $request->usertype,
            'password' => $password,
            'usergroup' => null,
            'user_status' => 'active',
            'user_role' => $request->user_role ?? null,
            'login' => 'no',
            'promo_tag' => '0',
            'store_assigned' => $request->store_assigned ? $request->store_assigned : null,
            'date_created' => now(),
            'user_addby' => $request->user()->user_id,
            'retail_group' => $request->retail_group ? $request->retail_group : null,
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

        $defaultPassword = 'GC2025';
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
        // dd($request->all());
        $status = Store::findOrFail($request->id);
        $status->update([
            'issuereceipt' => $status['issuereceipt'] == 'yes' ? 'no' : 'yes'
        ]);
        return back()->with(
            'success',
            "{$status->store_name} Issue Receipt updated successfully"
        );
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
        // Institutional Customer
        $institutional = InstitutCustomer::query()
            ->leftJoin('users', 'users.user_id', '=', 'institut_customer.ins_by')
            ->select(
                'institut_customer.*',
                DB::raw("CONCAT(users.firstname,  ' ', users.lastname)as createdBy_fullname")
            )
            ->when($request->institutionalSearch, function ($query, $search) {
                $query->where('ins_name', 'like', '%' . $search . '%')
                    ->orWhere('ins_custype', 'like', '%' . $search . '%')
                    ->orWhere('ins_gctype', 'like', '%' . $search . '%')
                    ->orWhere('ins_date_created', 'like', '%' . $search . '%')
                    ->orWhere(DB::raw("CONCAT(users.firstname,  ' ', users.lastname)"), 'like', '%' . $search . '%')
                    ->orWhere('ins_status', 'like', '%' . $search . '%');
            })->paginate(10);
        $gc_type = [
            1 => 'Regular',
            2 => 'Special',
            3 => 'Special External',
            4 => 'Promo',
            5 => 'Supplier GC',
            6 => 'Beam and Go'
        ];
        $institutional->transform(function ($item) use ($gc_type) {
            // $item->created_by = User::where('user_id', $item->ins_by)->first();
            // $item->createdBy_fullname = $item->created_by->firstname . ' ' . $item->created_by->lastname;
            $item->gcType = $gc_type[$item->ins_gctype] ?? '';
            $item->dateCreated = Date::parse($item->ins_date_created)->format('F j, Y, h:i A');

            return $item;
        });

        $InstitutionalColumns = array_map(
            fn($name, $field) => ColumnHelper::arrayHelper($name, $field),
            ['Name', 'Customer Type', 'GC Type', 'Date Created', 'Created By', 'Status', 'Action'],
            ['ins_name', 'ins_custype', 'gcType', 'dateCreated', 'createdBy_fullname', 'ins_status', 'action']
        );

        // Regular Customer
        $regularCustomer = Customer::query()
            ->leftJoin('stores', 'stores.store_id', '=', 'customers.cus_store_register')
            ->leftJoin('users', 'users.user_id', '=', 'customers.cus_register_by')
            ->select([
                'customers.*',
                'stores.store_name as storeRegistered',
                DB::raw("CONCAT(users.firstname, ' ', users.lastname) as registeredBy_fullname"),
            ])
            ->when($request->regularSearch, function ($query, $search) {
                $query->where(DB::raw("CONCAT(customers.cus_fname, ' ', customers.cus_lname)"), 'like', '%' . $search . '%')
                    ->orWhere('customers.cus_fname', 'like', '%' . $search . '%')
                    ->orWhere('customers.cus_lname', 'like', '%' . $search . '%')
                    ->orWhere('stores.store_name', 'like', '%' . $search . '%')
                    ->orWhere(DB::raw("CONCAT(users.firstname, ' ', users.lastname)"), 'like', '%' . $search . '%')
                    ->orWhere('customers.cus_register_at', 'like', '%' . $search . '%');
            })
            ->paginate(10)
            ->withQueryString();

        $regularCustomer->transform(function ($item) {
            $item->registeredAt = Date::parse($item->cus_register_at)->format('F j, Y, h:i A');
            return $item;
        });

        $regularCustomerColumns = array_map(
            fn($name, $field) => ColumnHelper::arrayHelper($name, $field),
            ['Firstname', 'Lastname', 'Store Registered', 'Registered At', 'Registered By', 'Action'],
            ['cus_fname', 'cus_lname', 'storeRegistered', 'registeredAt', 'registeredBy_fullname', 'action']
        );

        // Special External Customer
        $specialCustomer = SpecialExternalCustomer::when($request->specialCustomerSearch, function ($query, $search) {
            $query->where('spcus_companyname', 'like', '%' . $search . '%')
                ->orWhere('spcus_acctname', 'like', '%' . $search . '%')
                ->orWhere('spcus_address', 'like', '%' . $search . '%')
                ->orWhere('spcus_cperson', 'like', '%' . $search . '%')
                ->orWhere('spcus_cnumber', 'like', '%' . $search . '%')
                ->orWhere('spcus_type', 'like', '%' . $search . '%');
        })->paginate(10);
        $sp_type = [
            1 => 'Internal',
            2 => 'External',
        ];
        $specialCustomer->transform(function ($item) use ($sp_type) {
            $item->special_type = $sp_type[$item->spcus_type] ?? '';
            return $item;
        });
        $specialCustomerColumns = array_map(
            fn($name, $field) => ColumnHelper::arrayHelper($name, $field),
            ['Company Name', 'Account Name', 'Address', 'Contact Person', 'Contact Number', 'Type', 'Action'],
            ['spcus_companyname', 'spcus_acctname', 'spcus_address', 'spcus_cperson', 'spcus_cnumber', 'special_type', 'action']
        );
        $store = store::get();

        return inertia('Admin/Masterfile/Customer-Setup', [
            'regularCustomer' => $regularCustomer,
            'regularCustomerColumns' => $regularCustomerColumns,
            'regularSearch' => $request->regularSearch,
            'institutional' => $institutional,
            'institutinalColumns' => $InstitutionalColumns,
            'institutionalSearch' => $request->institutionalSearch,
            'specialCustomer' => $specialCustomer,
            'specialCustomerColumns' => $specialCustomerColumns,
            'specialCustomerSearch' => $request->specialCustomerSearch,
            'activeTabs' => $request->activeKey,
            'store' => $store
        ]);
    }
    public function updateCustomerStoreRegister(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'store' => 'required'
        ]);

        $store = [
            'Alturas Mall' => 1,
            'Alturas Talibon' => 2,
            'Island City Mall' => 3,
            'Plaza Marcela' => 4,
            'Alturas Tubigon' => 5,
            'Colonade Colon' => 6,
            'Colonade Mandaue' => 7,
            'Alta Citta' => 8,
            'Farmers Market' => 9,
            'Ubay Distribution Center' => 10,
            'Screenville' => 11,
            'Asc Tech' => 12
        ];

        $customer = Customer::findOrFail($request->id);
        if (
            $customer->cus_fname == $request->firstname &&
            $customer->cus_lname == $request->lastname &&
            $customer->cus_store_register == $store[$request->store]
        ) {
            return back()->with(
                'error',
                "{$request->firstname} {$request->lastname} data has no changes, please update before submitting."
            );
        }


        Customer::where('cus_id', $request->id)->update([
            'cus_fname' => $request->firstname,
            'cus_lname' => $request->lastname,
            'cus_store_register' => is_numeric($request->store) ? $request->store : $store[$request->store] ?? 0
        ]);
        return back()->with(
            'success',
            'Regular customer updated successfully'
        );
    }

    public function deleteRegularCustomer(Request $request)
    {
        Customer::where('cus_id', $request->id)->delete();
        return back()->with(
            'success',
            'Regular customer deleted successfully'
        );
    }
    public function setupStore(Request $request)
    {
        $data = Store::when($request->search, function ($query, $search) {
            $query->where('store_name', 'like', '%' . $search . '%')
                ->orWhere('store_code', 'like', '%' . $search . '%')
                ->orWhere('company_code', 'like', '%' . $search . '%');
        })->paginate(10);

        $columns = array_map(
            fn($name, $field) => ColumnHelper::arrayHelper($name, $field),
            ['Store Name', 'Store Code', 'Company Code', 'Default Password', 'Issue Receipt'],
            ['store_name', 'store_code', 'company_code', 'default_password', 'action',]
        );
        return inertia('Admin/Masterfile/Setup-Store', [
            'data' => $data,
            'search' => $request->search,
            'columns' => $columns
        ]);
    }
    public function saveStore(Request $request)
    {

        $request->validate([
            'store_name' => 'required|string|max:50',
            'store_code' => 'required|integer',
            'company_code' => 'required|integer',
            'default_password' => 'required'
        ]);

        Store::create([
            'store_name' => $request->store_name,
            'store_code' => $request->store_code,
            'company_code' => $request->company_code,
            'default_password' => $request->default_password,
            'store_status' => '',
            'issuereceipt' => '',
            'store_bng' => '',
            'has_local' => '1',
            'store_textfile_ip' => '',
            'store_initial' => '',
        ]);
        return back()->with(
            'success',
            'Store added successfully'
        );
    }

    public function creditCardSetup(Request $request)
    {
        $data = CreditCard::when($request->data, function ($query, $search) {
            $query->where('ccard_name', 'like', '%' . $search . '%')
                ->orWhere('ccard_status', 'like', '%' . $search . '%')
                ->orWhere('ccard_created', 'like', '%' . $search . '%')
                ->orWhere('ccard_by', 'like', '%' . $search . '%');
        })->paginate(10);

        $data->transform(function ($item) {
            $item['ccard_created_formatted'] = Carbon::parse($item['ccard_created'])->format('F d, Y');
            $item->ccardBy = User::where('user_id', $item->ccard_by)->first();
            $item->ccardBy_fullname = ($item->ccardBy->firstname ?? $item->ccard_by) . ' ' . ($item->ccardBy->lastname ?? $item->ccard_by);
            return $item;
        });

        return inertia('Admin/Masterfile/SetupCreditCard', [
            'data' => $data,
            'search' => $request->data,
        ]);
    }

    public function saveCreditCard(Request $request)
    {
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
            'denom_fad_item_number' => 'required|integer'
        ]);

        $denom_code = Denomination::max('denom_code');
        $newDenomCode = $denom_code ? $denom_code + 1 : 1;

        $newDenom = Denomination::where('denomination', $request->denomination)->first();
        if ($newDenom) {
            return back()->with('error', 'Denomination already exist');
        }
        $isSuccessful = Denomination::create([
            'denomination' => $request->denomination,
            'denom_barcode_start' => $request->denom_barcode_start,
            'denom_dateupdated' => now(),
            'denom_code' => $newDenomCode,
            'denom_fad_item_number' => $request->denom_fad_item_number,
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
        $data = $data->orderBy('store_id', 'ASC')
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
            'id' => 'required|int',
            'name' => 'required|string',
            'customerType' => 'required|string',
            'gcType' => 'required',
            'status' => 'required'
        ]);
        $gcType = [
            'Regular' => 1,
            'Special' => 2,
            'Special External' => 3,
            'Promo' => 4,
            'Supplier GC' => 5,
            'Beam and Go' => 6
        ];

        $checkUpdate = InstitutCustomer::findOrFail($request->id);
        if ($checkUpdate) {
            $checkData =
                $checkUpdate->ins_name == $request->name && $checkUpdate->ins_custype == $request->customerType
                && $checkUpdate->ins_gctype == $gcType[$request->gcType] && $checkUpdate->ins_status == $request->status;

        }
        if ($checkData) {
            return back()->with(
                'error',
                "{$request->name} data has no changes happen, please update first before submitting"
            );
        }

        InstitutCustomer::where('ins_id', $request->id)->update([
            'ins_name' => $request->name,
            'ins_custype' => $request->customerType,
            'ins_gctype' => is_numeric($request->gcType) ? $request->gcType : $gcType[$request->gcType],
            'ins_status' => $request->status
        ]);

        return back()->with(
            'success',
            'Institute customer updated successfully'
        );
    }

    public function deleteInstitutionalCustomer(Request $request)
    {
        InstitutCustomer::where('ins_id', $request->id)->delete();
        return back()->with(
            'success',
            'Institutional customer deleted successfully'
        );
    }
    public function updateSpecialCustomer(Request $request)
    {
        $request->validate([
            'id' => 'required|int',
            'companyname' => 'required|string',
            'acctname' => 'required|string',
            'address' => 'required|string',
            'cperson' => 'required|string',
            'cnumber' => 'required|string',
            'type' => 'required|string',
        ]);
        $sp_type = [
            'Internal' => 1,
            'External' => 2
        ];

        $checkUpdate = SpecialExternalCustomer::findOrFail($request->id);
        if ($checkUpdate) {
            $checkData =
                $checkUpdate->spcus_companyname == $request->companyname && $checkUpdate->spcus_acctname == $request->acctname
                && $checkUpdate->spcus_address == $request->address && $checkUpdate->spcus_cperson == $request->cperson
                && $checkUpdate->spcus_cnumber == $request->cnumber && $checkUpdate->spcus_type == $sp_type[$request->type];
        }
        if ($checkData) {
            return back()->with(
                'error',
                "{$request->companyname} data has no changes happen, please update first before submitting"
            );
        }
        $checkUpdate->update([
            'spcus_companyname' => $request->companyname,
            'spcus_acctname' => $request->acctname,
            'spcus_address' => $request->address,
            'spcus_cperson' => $request->cperson,
            'spcus_cnumber' => $request->cnumber,
            'spcus_type' => is_numeric($request->type) ? $request->type : $sp_type[$request->type]
        ]);

        return back()->with(
            'success',
            'Special customer updated successfully'
        );
    }

    public function deleteSpecialCustomer(Request $request)
    {
        SpecialExternalCustomer::where('spcus_id', $request->id)->delete();
        return back()->with(
            'success',
            'Special Customer deleted successfully'
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

    public function pass(Request $request)
    {
        $pass = 'wefindways';
        if ($request->password == $pass) {
            return back()->with(
                'success',
                'Access Granted'
            );
        } else {
            return back()->with(
                'error',
                'Access Denied'
            );
        }

    }

    public function deleteUser(Request $request)
    {
        User::where('user_id', $request->id)->delete();
        return back()->with(
            'success',
            'User deleted successfully'
        );
    }
}
