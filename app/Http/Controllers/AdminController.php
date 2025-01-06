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
use Illuminate\Support\Facades\DB;
use Symfony\Contracts\Service\Attribute\Required;

class AdminController extends Controller
{
    public function __construct(public AdminServices $adminservices, public DBTransaction $dBTransaction) {}

    public function index()
    {
        $users = User::count();
        return inertia('Admin/AdminDashboard', [
            'users' => $users
        ]);
    }
    //
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
    public function barcodeStatus() {}



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
        $StoreStaff = StoreStaff::get();
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
            'users.retail_group'
        )
            ->join('access_page', 'users.usertype', '=', 'access_page.access_no')
            ->leftJoin('stores', 'users.store_assigned', '=', 'stores.store_id');

        $searchTerm = $request->input('data', '');


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

        $noDataFound = false;
        if ($searchTerm && $users->isEmpty()) {
            $noDataFound = true;
        }

        $users->transform(function ($item) {
            $item->status = $item->user_status == 'active';
            return $item;
        });

        return Inertia::render('Admin/Masterfile/Users', [
            'users' => $users,
            'search' => $request->data,
            'access_page' => $access_page,
            'storestaff' => $StoreStaff,
            'noDataFound' => $noDataFound,
            'store' => $Store,
            'value' => $request->value,
            'filterstore' => $request->store
        ]);
    }
    public function updateUser(Request $request)
    {

        // dd($request->all());
        // dd($request->usertype);


        $request->validate([
            'username' => 'required|max:50',
            'firstname' => 'required|max:50',
            'lastname' => 'required|max:50',
            'emp_id' => 'required',
            'usertype' => 'required',
            'store_assigned' => [
                'sometimes',
                Rule::requiredIf(function () use ($request) {
                    $usertype = $request->input('usertype');
                    $it_type = $request->input('it_type');

                    $non_required_usertypes = [
                        1,
                        'administrator',
                        2,
                        'treasurydept',
                        3,
                        'finance',
                        4,
                        'custodian',
                        5,
                        'generalmanager',
                        6,
                        'marketing',
                        8,
                        'retailgroup',
                        9,
                        'accounting',
                        10,
                        'internal_audit',
                        11,
                        'finance_office',
                        12,
                        'it_personnel',
                        13,
                        'cfs'
                    ];

                    $is_in_non_required = in_array($usertype, $non_required_usertypes);

                    $is_required = in_array($usertype, ['7', 'retailstore', 'store_accounting', '14']) ||
                        in_array($it_type, ['2', 'store_it',]);

                    return !$is_in_non_required && $is_required;
                })
            ],

            'user_role' => [
                'sometimes',
                Rule::requiredIf(function () use ($request) {
                    return $request->input('it_type') !== '2';
                })
            ],
            'retail_group' => [
                'sometimes',
                Rule::requiredIf(function () use ($request) {
                    return $request->input('usertype') === 'retailgroup' || $request->input('usertype') === 8;
                })
            ],
            'it_type' => [
                'sometimes',
                Rule::requiredIf(function () use ($request) {
                    return $request->input('usertype') === 'it_personnel' || $request->input('usertype') === 12;
                })
            ]
        ]);


        $userType = AccessPage::where('employee_type', $request->usertype)->first();
        if ($userType) {
            $user_type = $userType->access_no;
        } else {
            $user_type = $request->usertype;
        }



        $storeAssigned = Store::where('store_name', $request->store_assigned)->first();
        if ($storeAssigned) {
            $store_Assigned = $storeAssigned->store_code;
        } else {
            $store_Assigned = $request->store_assigned;
        }

        $updatedData = [
            'username' => $request->username,
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'emp_id' => $request->emp_id,
            'usertype' => $user_type,
            'user_role' => $request->user_role,
            'user_status' => $request->user_status,
        ];

        if ($request->usertype !== 12 && $request->usertype !== 'it_personnel') {
            $updatedData['it_type'] = null;
        } else {
            $updatedData['it_type'] = $request->it_type;
        }

        if ($request->usertype !== 8 && $request->usertype !== 'retailgroup') {
            $updatedData['retail_group'] = null;
        } else {
            $updatedData['retail_group'] = $request->retail_group;
        }
        if (
            $request->usertype !== 7 && $request->usertype !== 'retailstore' && $request->usertype !== 14
            && $request->usertype !== 'store_accounting' && $request->it_type !== '2' || $request->usertype === 1 || $request->usertype === 2
            || $request->usertype === 3 || $request->usertype === 4 || $request->usertype === 5 || $request->usertype === 6
            || $request->usertype === 8 || $request->usertype === 9 || $request->usertype === 10 || $request->usertype === 11
            || $request->usertype === 13
        ) {
            $updatedData['store_assigned'] = 0;
        } else {
            $updatedData['store_assigned'] = $store_Assigned;
        }
        // dd($request->it_type);

        $user = User::findOrFail($request->user_id);
        if (
            $user->username == $request->username &&
            $user->firstname == $request->firstname &&
            $user->lastname == $request->lastname &&
            $user->emp_id == $request->emp_id &&
            $user->usertype == $request->usertype &&
            $user->user_role == $request->user_role &&
            $user->user_status == $request->user_status &&
            $user->it_type == $request->it_type &&
            $user->store_it == $request->store_it &&
            $user->it_personnel == $request->it_personnel &&
            $user->retail_group == $request->retail_group &&
            $user->store_assigned == $request->store_assigned
        ) {
            return back()->with('error', 'WARNING');
        }

        $onSuccess = User::where('user_id', $request->user_id)->update($updatedData);


        if ($onSuccess) {
            return back()->with('success', 'SUCCESS');
        }
        return back()->with('error', 'OPPS');
    }


    public function users_save_user(Request $request)
    {

        $request->validate([
            'username' => 'required|max:50',
            'firstname' => 'required|max:50',
            'lastname' => 'required|max:50',
            'emp_id' => 'required|integer',
            'employee_type' => 'required',
            'user_role' => [
                Rule::requiredIf(function () use ($request) {
                    return $request->input('it_type') !== 'store_it';
                }),
            ],
            'it_type' => [
                'sometimes',
                Rule::requiredIf(function () use ($request) {
                    return $request->input('employee_type') === 'it_personnel';
                }),
            ],
            'retail_group' => [
                'sometimes',
                Rule::requiredIf(function () use ($request) {
                    return $request->input('employee_type') === 'retailgroup';
                }),
            ],
            'store_name' => [
                'sometimes',
                Rule::requiredIf(function () use ($request) {
                    return $request->input('employee_type') === 'retailstore'
                        || $request->input('employee_type') === 'store_accounting'
                        || $request->input('it_type') === 'store_it';
                }),
            ]
        ]);

        $storeAssigned = Store::where('store_name', $request->store_name)->first();

        if ($storeAssigned === null) {
            $store_assigned = '0';
        } else {
            $store_assigned = $storeAssigned->store_code;
        }

        $accessPage = AccessPage::where('employee_type', $request->employee_type)->first();

        if ($accessPage === null) {
            $usertype = '0';
        } else {
            $usertype = $accessPage->access_no;
        }
        $newUser = User::where('username', $request->username)->first();
        if ($newUser) {
            return back()->with(
                'error',
                'OPPS'
            );
        }

        $password = Hash::make($request->ss_password);

        $isSuccessful = User::create([
            'username' => $request->username,
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'emp_id' => $request->emp_id,
            'usertype' => $usertype,
            'password' => $password,
            'usergroup' => '',
            'user_status' => 'active',
            'user_role' => $request->user_role,
            'login' => 'no',
            'promo_tag' => '0',
            'store_assigned' => $store_assigned,
            'date_created' => now(),
            'user_addby' => $request->user()->user_id,
            'retail_group' => $request->retail_group,
            'it_type' => $request->it_type

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

    public function usersResetPassword(Request $request)
    {
        // dd($request->password);

        $defaultPassword = 'GC2015';

        $user = User::where('user_id', $request->user_id)->first();
        if (!$user) {
            return back()->with(
                'error',
                'User not found'
            );
        }
        if (Hash::needsRehash($user->password)) {
            $user->password = Hash::make($user->password);
            $user->save();
            return back()->with(
                'success',
                'Password was rehashed successfully'
            );
        }

        if (Hash::check($defaultPassword, $user->password)) {
            return back()->with(
                'error',
                'Opps'
            );
        }

        $newPassword = Hash::make($defaultPassword);
        $onSuccess = $user->update(['password' => $newPassword]);
        if ($onSuccess) {
            return back()->with('success', 'Password reset successfully.');
        } else {
            return back()->with('error', 'Failed to reset the password.');
        }
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

        return Inertia::render('Admin/Masterfile/StoreStaffSetup', [
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
            'username' => 'required|max:50',
            'firstname' => 'required',
            'lastname' => 'required',
            'employee_id' => 'required|integer',
            'password' => 'required',
            'store_id' => 'required',
            'usertype' => 'required',
        ]);

        $storeStaff = StoreStaff::where('ss_username', $request->username)->first();
        if ($storeStaff) {
            return back()->with(
                'error',
                'OPPS'
            );
        }

        $pass = Hash::make($request->ss_password);

        $isSuccessfull = StoreStaff::create([
            'ss_username' => $request->username,
            'ss_firstname' => $request->firstname,
            'ss_lastname' => $request->lastname,
            'ss_idnumber' => $request->employee_id,
            'ss_password' => $pass,
            'ss_usertype' => $request->usertype,
            'ss_status' => 'active',
            'ss_store' => $request->store_id,
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
                ->paginate(10)
                ->withQueryString();
        } elseif ($activeTab === 'institutional_customer') {
            $data =  $data->whereNotNull('institut_customer.ins_id')
                ->orderBy('institut_customer.ins_id', 'ASC')
                ->paginate(10)
                ->withQueryString();
        } elseif ($activeTab === 'special_customer') {
            $data = $data->whereNotNull('special_external_customer.spcus_id')
                ->orderBy('special_external_customer.spcus_id', 'ASC')
                ->paginate(10)
                ->withQueryString();
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
        // dd($request->all());
        $searchTerm = $request->input('data', '');
        $selectEntries = $request->input('value', 10);

        $data = creditcard::query();

        if ($searchTerm) {
            $data->where(function ($query) use ($searchTerm) {
                $query->where('ccard_name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('ccard_status', 'like', '%' . $searchTerm . '%')
                    ->orWhere('ccard_created', 'like', '%' . $searchTerm . '%')
                    ->orWhere('ccard_by', 'like', '%' . $searchTerm . '%');
            });
        }
        $data = $data->orderByDesc('ccard_id')
            ->paginate($selectEntries)
            ->withQuerystring();

        return inertia('Admin/Masterfile/CreditCardSetup', [
            'data' => $data,
            'search' => $request->data,
            'value' => $request->value
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
                'SUCCESS'
            );
        }
        return back()->with(
            'error',
            'FAILED TO ADD'
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
            ->paginate(10)
            ->withQueryString();

        return inertia('Admin/Masterfile/DenominationSetup', [
            'data' => $data,
            'search' => $request->data,
            'value' => $request->value
        ]);
    }


    public function saveDenomination(Request $request)
    {
        // dd($request->toArray());
        $request->validate([
            'denomination' => 'required|integer',
            'barcodeNumStart' => 'required|integer',
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
            'denom_barcode_start' => $request->barcodeNumStart,
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
                'SUCCESS'
            );
        }
        return back()->with(
            'error',
            'FAILED TO SAVE'
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
            return back()->with('error', 'OPPS');
        }


        $isSuccessfull = Denomination::where('denom_id', $request->denom_id)->update([
            'denomination' => $request->denomination,
            'denom_barcode_start' => $request->denom_barcode_start,
            'denom_fad_item_number' => $request->denom_fad_item_number
        ]);
        if ($isSuccessfull) {
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
                'SUCCESS'
            );
        }
        return back()->with(
            'error',
            'OPPS'
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
                'OPPS'
            );
        }
        $newPassword = Hash::make($defaultPassword);

        $onSuccess = StoreStaff::where('ss_id', $request->ss_id)->update([
            'ss_password' => $newPassword
        ]);
        if ($onSuccess) {
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
            ->withQuerystring();

        return Inertia::render('Admin/Masterfile/RevolvingFund', [
            'data' => $data,
            'search' => $request->data,
            'value' => $request->value
        ]);
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
