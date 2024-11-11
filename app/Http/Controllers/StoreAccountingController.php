<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use Inertia\Inertia;
use App\Models\Store;
use App\Models\Customer;
use App\Models\StoreEod;
use Barryvdh\DomPDF\PDF;
use App\Models\GcRelease;
use App\Models\StoreStaff;
use App\Models\Denomination;
use App\Models\StoreEodItem;
use Illuminate\Http\Request;
use App\Models\InstitutPayment;
use App\Models\TransactionSale;
use App\Models\InstitutCustomer;
use App\Models\TransactionStore;
use App\Models\ApprovedGcrequest;
use App\Models\StoreVerification;
use Illuminate\Support\Facades\DB;
use App\Models\InstitutTransaction;
use App\Models\SpecialExternalCustomer;
use App\Models\InstitutTransactionsItem;
use App\Models\SpecialExternalGcrequest;
use App\Models\StoreEodTextfileTransaction;
use App\Models\SpecialExternalGcrequestItem;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\SpecialExternalGcrequestEmpAssign;

class StoreAccountingController extends Controller
{
    public function storeAccountingDashboard(Request $request)
    {
        $searchTerm = $request->input('data', '');
        $selectEntries = $request->input('value', 10);


        $searchQuery = StoreEod::select(
            'steod_id',
            'steod_storeid',
            'steod_by',
            'steod_datetime',
            'store_id',
            User::raw("concat(users.firstname, ' ',users.lastname) as fullname"),
            StoreEod::raw('CASE WHEN steod_storeid = 0 THEN "All Stores" ELSE stores.store_name END as store_name')
        )
            ->leftJoin('stores', 'steod_storeid', '=', 'stores.store_id')
            ->leftJoin('users', 'steod_by', '=', 'users.user_id');


        if ($searchTerm) {
            $searchQuery->where(function ($query) use ($searchTerm) {
                $query->where('stores.store_name', 'like', '%' . $searchTerm . '%')
                    ->orWhereRaw("CONCAT(users.firstname, ' ', users.lastname) LIKE ?", ["%{$searchTerm}%"]);
            });
        }
        // dd($searchTerm);


        $data = $searchQuery->orderByDesc('steod_id')
            ->paginate($selectEntries)
            ->withQueryString();


        return inertia('StoreAccounting/StoreAccountingDashboard', [
            'data' => $data,
            'pagination' => $selectEntries,
            'search' => $request->data
        ]);
    }


    public function storeeod(Request $request, $id)
    {
        // dd($eodDate = $request->input('eodDate', ''));
        $searchTerm = $request->input('search', '');
        $selectEntries = $request->input('value', 10);
        $eodDate = $request->input('eodDate', '');

        $searchQuery = StoreEodItem::select(
            'st_eod_id',
            'store_verification.vs_barcode as vs_barcode',
            'store_verification.vs_cn as vs_cn',
            'vs_tf_denomination',
            'vs_tf_balance',
            'vs_date',
            'vs_time',
            'vs_cn',
            'vs_store',
            'store_name',
            User::raw("concat(users.firstname, ' ', users.lastname) as users_fullname"),
            Customer::raw("concat(customers.cus_fname, ' ', customers.cus_lname) as cus_fullname"),
            StoreEodItem::raw("concat(vs_date, ' ', vs_time) as storeEodDateTime"),


        )
            ->join('store_verification', 'store_verification.vs_barcode', '=', 'store_eod_items.st_eod_barcode')
            ->join('customers', 'customers.cus_id', '=', 'store_verification.vs_cn')
            ->join('users', 'users.user_id', '=', 'store_verification.vs_by')
            ->join('gc_type', 'gc_type.gc_type_id', '=', 'store_verification.vs_gctype')
            ->join('stores', 'stores.store_id', '=', 'store_verification.vs_store')
            ->where('st_eod_trid', $id);


        if ($searchTerm) {
            $searchQuery->where(function ($query) use ($searchTerm) {
                $query->where('st_eod_barcode', 'like', '%' . $searchTerm . '%')
                    ->orWhere('store_name', 'like', '%' . $searchTerm . '%')
                    ->orWhereRaw("CONCAT(users.firstname, ' ', users.lastname) LIKE ?", ["%{$searchTerm}%"])
                    ->orWhereRaw("CONCAT(customers.cus_fname, ' ', customers.cus_lname)LIKE ?", ["%{$searchTerm}%"])
                    ->orWhereRaw("CONCAT(vs_date, ' ', vs_time)LIKE ?", ["%{$searchTerm}%"])
                ;
            });
        }


        $data = $searchQuery->orderByDesc('st_eod_id')
            ->paginate($selectEntries)
            ->withQueryString();

        return inertia('StoreAccounting/StoreAccountingEod', [
            'data' => $data,
            'pagination' => $request->value,
            'search' => $searchTerm,
            'eodDate' => $eodDate,
            'ideod' => $id,

        ]);
    }


    public function GCNavisionPOSTtransactions($barcode)
    {
        // dd($barcode);

        $data = StoreEodTextfileTransaction::select(
            'seodtt_line',
            'seodtt_creditlimit',
            'seodtt_credpuramt',
            'seodtt_addonamt',
            'seodtt_balance',
            'seodtt_transno',
            'seodtt_timetrnx',
            'seodtt_bu',
            'seodtt_terminalno',
            'seodtt_ackslipno',
            'seodtt_crditpurchaseamt'
        )
            ->where('seodtt_barcode', $barcode)
            ->orderBy('seodtt_id', 'ASC')
            ->get();

        return response()->json($data);
    }
    public function storeAccoutingSales(Request $request)
    {
        $pagination = $request->input('entries', 10);
        $searchTerm = $request->input('search', '');
        $query = InstitutPayment::select(
            'insp_id',
            'insp_trid',
            'insp_paymentcustomer',
            'institut_bankname',
            'institut_bankaccountnum',
            'institut_checknumber',
            'institut_amountrec',
            'insp_paymentnum',
            'institut_eodid'
        );

        if ($searchTerm) {
            $query->where(function ($query) use ($searchTerm) {
                $query->where('insp_paymentnum', 'like', '%' . $searchTerm . '%')
                    ->orWhere(function ($query) use ($searchTerm) {
                        $query->orWhere('insp_paymentcustomer', 'like', '%' . $searchTerm . '%');

                        if (stripos('Institution GC', $searchTerm) !== false) {
                            $query->orWhere('insp_paymentcustomer', 'institution');
                        }
                        if (stripos('Regular GC', $searchTerm) !== false) {
                            $query->orWhere('insp_paymentcustomer', 'stores');
                        }
                        if (stripos('Special External GC', $searchTerm) !== false) {
                            $query->orWhere('insp_paymentcustomer', 'special external');
                        }
                    });
            });
        }

        $searchQuery = $query->orderByDesc('insp_id')
            ->paginate($pagination)
            ->withQueryString();

        // dd($searchQuery);

        foreach ($searchQuery as $payment) {
            // dd($payment);
            if ($payment->insp_paymentcustomer == 'institution') {
                // dd($payment->insp_paymentcustomer);
                $payment->insp_paymentcustomer = 'Institution GC';
                $transaction = InstitutTransaction::select(
                    'institut_transactions.institutr_id',
                    'institut_transactions.institutr_trnum',
                    'institut_transactions.institutr_paymenttype',
                    'institut_transactions.institutr_date',
                    'institut_customer.ins_name'
                )
                    ->join('institut_customer', 'institut_customer.ins_id', '=', 'institut_transactions.institutr_cusid')
                    ->where('institutr_id', $payment->insp_trid)->first();

                $payment->customer = $transaction->ins_name ?? '';
                // dd($data->ins_name);
                $payment->datetr = $transaction->institutr_date
                    ? Carbon::parse($transaction->institutr_date)->toDateString() : '';
                $payment->timetr = $transaction->institutr_date
                    ? Carbon::parse($transaction->institutr_date)->format('h:i A') : '';
                $payment->paymenttype = $transaction->institutr_paymenttype ?? '';


                $gcData = InstitutTransactionsItem::where('instituttritems_trid', $payment->insp_trid)
                    ->join('gc', 'gc.barcode_no', '=', 'institut_transactions_items.instituttritems_barcode')
                    ->join('denomination', 'denomination.denom_id', '=', 'gc.denom_id')
                    ->selectRaw('COUNT(instituttritems_barcode) as cnt, SUM(denomination.denomination) as totamt')->first();

                $payment->totgccnt = $gcData->cnt ?? 0;
                $payment->totdenom = $gcData->totamt ?? 0;

                // if store statement-----------------------------------------------------------------------------------

            } elseif ($payment->insp_paymentcustomer == 'stores') {
                // dd  ($payment->insp_paymentcustomer);
                $payment->insp_paymentcustomer = 'Regular GC';
                $transaction = ApprovedGcrequest::select(
                    'approved_gcrequest.agcr_request_id',
                    'approved_gcrequest.agcr_request_relnum',
                    'approved_gcrequest.agcr_approved_at',
                    'approved_gcrequest.agcr_paymenttype',
                    'stores.store_name'
                )
                    ->join('store_gcrequest', 'store_gcrequest.sgc_id', '=', 'approved_gcrequest.agcr_request_id')
                    ->join('stores', 'stores.store_id', '=', 'store_gcrequest.sgc_store')
                    ->select(
                        'approved_gcrequest.agcr_request_id',
                        'approved_gcrequest.agcr_request_relnum',
                        'approved_gcrequest.agcr_approved_at',
                        'approved_gcrequest.agcr_paymenttype',
                        'stores.store_name'
                    )
                    ->where('agcr_id', $payment->insp_trid)

                    ->first();
                // dd($payment->insp_trid);

                $payment->customer = $transaction->store_name ?? '';
                $payment->datetr = $transaction->agcr_approved_at
                    ? Carbon::parse($transaction->institutr_date)->toDateString() : '';

                $payment->timetr = $transaction->institutr_date
                    ? Carbon::parse($transaction->institutr_date)->format('h:i A') : '';
                $payment->paymenttype = $transaction->agcr_paymenttype ?? '';
                // dd($payment->timetr = $transaction->institutr_date

                $gcData = GcRelease::join('gc', 'gc.barcode_no', '=', 'gc_release.re_barcode_no')
                    ->join('denomination', 'denomination.denom_id', '=', 'gc.denom_id')
                    ->selectRaw('COUNT(gc_release.re_barcode_no) as cnt, SUM(denomination.denomination) as totamt')
                    ->where('rel_num', $transaction->agcr_request_relnum)->first();

                $payment->totgccnt = $gcData->cnt ?? 0;
                $payment->totdenom = $gcData->totamt ?? 0;

                // if special external statement-----------------------------------------------------------

            } elseif ($payment->insp_paymentcustomer == 'special external') {
                // dd($payment->insp_paymentcustomer);
                $payment->insp_paymentcustomer = 'Special External GC';
                $transaction = SpecialExternalGcrequest::select(
                    'special_external_gcrequest.spexgc_id',
                    'special_external_gcrequest.spexgc_datereq',
                    'special_external_customer.spcus_companyname',
                    'special_external_gcrequest.spexgc_paymentype',
                    'special_external_gcrequest.spexgc_addemp'
                )
                    ->join('special_external_customer', 'special_external_customer.spcus_id', '=', 'special_external_gcrequest.spexgc_company')
                    ->where('special_external_gcrequest.spexgc_id', $payment->insp_trid)
                    ->first();

                if ($transaction) {

                    if ($transaction->spexgc_addemp === 'pending') {
                        continue;
                    }
                    $payment->customer = $transaction->spcus_companyname ?? '';
                    $payment->datetr = $transaction->spexgc_datereq
                        ? Carbon::parse($transaction->institutr_date)->toDateString() : '';

                    $payment->timetr = $transaction->institutr_date
                        ? Carbon::parse($transaction->institutr_date)->format('h:i A') : '';
                    $payment->paymenttype = $transaction->spexgc_paymentype == '1' ? 'cash' : 'check';

                    $gcData = SpecialExternalGcrequestItem::selectRaw('SUM(specit_qty) as cnt, SUM(specit_denoms * specit_qty) as totamt')
                        ->where('specit_trid', $payment->insp_trid)
                        ->first();

                    $payment->totgccnt = $gcData->cnt ?? 0;
                    $payment->totdenom = $gcData->totamt ?? 0;
                } else {
                    $payment->customer = '';
                    $payment->datetr = '';
                    $payment->paymenttype = null;
                }
            }
        }
        return inertia('StoreAccounting/StoreAccountingSales', [
            'data' => $searchQuery,
            'pagination' => $pagination,
            'searchData' => $request->search
        ]);
    }

    public function storeaccountingViewSales(Request $request, $id)
    {
        // dd($id);
        $salesCustomer = $request->input('salesCustomer', '');
        $searchTerm = $request->input('search', '');
        // $pagination = $request->input('selectEntries', '');
        $payment = InstitutPayment::select(
            'insp_id',
            'insp_trid',
            'insp_paymentcustomer',
            'institut_bankname',
            'institut_bankaccountnum',
            'institut_checknumber',
            'institut_amountrec',
            'insp_paymentnum',
        )
            ->where('insp_id', $id)->first();

        $arr_barcodesinfo = [];

        if ($payment->insp_paymentcustomer == 'institution') {


            $mainData = InstitutTransaction::select(
                'institut_transactions.institutr_id',
                'institut_transactions.institutr_trnum',
                'institut_transactions.institutr_paymenttype',
                'institut_transactions.institutr_date',
                'institut_customer.ins_name'
            )
                ->join('institut_customer', 'institut_customer.ins_id', '=', 'institut_transactions.institutr_cusid')
                ->where('institut_transactions.institutr_id', $payment->insp_trid)
                ->get();


            // dd($payment->insp_trid);        
            if ($mainData) {

                $data = InstitutTransactionsItem::select(
                    'institut_transactions_items.instituttritems_barcode',
                    'denomination.denomination',
                    'stores.store_name',
                    'store_verification.vs_date',
                    User::raw("CONCAT(users.firstname, ' ', users.lastname) as fullname"),
                    Customer::raw("CONCAT(customers.cus_fname, ' ', customers.cus_lname) as customerFullname"),
                    'store_verification.vs_tf_used',
                    'store_verification.vs_reverifydate',
                    'store_verification.vs_reverifyby',
                    'store_verification.vs_tf_balance'
                )
                    ->join('gc', 'gc.barcode_no', '=', 'institut_transactions_items.instituttritems_barcode')
                    ->join('denomination', 'denomination.denom_id', '=', 'gc.denom_id')
                    ->leftJoin('store_verification', 'store_verification.vs_barcode', '=', 'institut_transactions_items.instituttritems_barcode')
                    ->leftJoin('stores', 'stores.store_id', '=', 'store_verification.vs_store')
                    ->leftJoin('users', 'store_verification.vs_by', '=', 'users.user_id')
                    ->leftJoin('customers', 'customers.cus_id', '=', 'store_verification.vs_cn')
                    ->where('instituttritems_trid', $payment->insp_trid);

                if ($searchTerm) {
                    $data = $data->where(function ($query) use ($searchTerm) {
                        $query->where('instituttritems_barcode', 'like', '%' . $searchTerm . '%')
                            ->orWhere('denomination', 'like', '%' . $searchTerm . '%')
                            ->orWhere('store_name', 'like', '%' . $searchTerm . '%')
                            ->orWhere('vs_date', 'like', '%' . $searchTerm . '%')
                            ->orWhere('vs_reverifydate', 'like', '%' . $searchTerm . '%')
                            ->orWhere('vs_reverifyby', 'like', '%' . $searchTerm . '%')
                            ->orWhere('vs_tf_used', 'like', '%' . $searchTerm . '%')
                            ->orWhere('vs_tf_balance', 'like', '%' . $searchTerm . '%')
                            ->orWhereRaw("CONCAT(users.firstname, ' ', users.lastname) LIKE ?", ["%{$searchTerm}%"])
                            ->orWhereRaw("CONCAT(customers.cus_fname, ' ', customers.cus_lname) LIKE ?", ["%{$searchTerm}%"]);
                    });
                }
                $data = $data->get();
                // dd($payment->insp_trid);
                // dd($data);

                foreach ($data as $newData) {
                    $arr_barcodesinfo[] = [
                        'barcode' => $newData->instituttritems_barcode,
                        'denomination' => $newData->denomination,
                        'store' => $newData->store_name,
                        'dateverify' => $newData->vs_date,
                        'verifyby' => $newData->fullname,
                        'rdateverify' => $newData->vs_reverifydate,
                        'rverifyby' =>  $newData->vs_reverifyby,
                        'customer' => $newData->customerFullname,
                        'used' => $newData->vs_tf_used,
                        'balanced' => $newData->vs_tf_balance,
                        'type' => 'Institution GC'
                    ];
                }
                // dd($arr_barcodesinfo);
            }
        }

        // elseif statement if the insp_paymentcustomer is equal to stores----------------------------

        elseif ($payment->insp_paymentcustomer == 'stores') {
            $mainData = ApprovedGcrequest::select(
                'approved_gcrequest.agcr_request_id',
                'approved_gcrequest.agcr_request_relnum',
                'approved_gcrequest.agcr_approved_at',
                'approved_gcrequest.agcr_paymenttype',
                'stores.store_name'
            )
                ->join('store_gcrequest', 'store_gcrequest.sgc_id', '=', 'approved_gcrequest.agcr_request_id')
                ->join('stores', 'stores.store_id', '=', 'store_gcrequest.sgc_store')
                ->where('approved_gcrequest.agcr_id', $payment->insp_trid)->first();

            // dd($payment->insp_trid);

            if ($mainData) {

                $data = GCRelease::select(
                    're_barcode_no',
                    'rel_num',
                    'denomination.denomination',
                    'stores.store_name',
                    'store_verification.vs_date',
                    User::raw("CONCAT(users.firstname, ' ', users.lastname) as fullname"),
                    Customer::raw("CONCAT(customers.cus_fname, ' ', customers.cus_lname) as customerFullname"),
                    'store_verification.vs_tf_used',
                    'store_verification.vs_reverifydate',
                    'store_verification.vs_reverifyby',
                    'store_verification.vs_tf_balance',
                )
                    ->join('gc', 'gc.barcode_no', '=', 'gc_release.re_barcode_no')
                    ->join('denomination', 'denomination.denom_id', '=', 'gc.denom_id')
                    ->leftJoin('store_verification', 'store_verification.vs_barcode', '=', 'gc_release.re_barcode_no')
                    ->leftJoin('stores', 'stores.store_id', '=', 'store_verification.vs_store')
                    ->leftJoin('users', 'store_verification.vs_by', '=', 'users.user_id')
                    ->leftJoin('customers', 'customers.cus_id', '=', 'store_verification.vs_cn')
                    ->where('gc_release.rel_num', $mainData->agcr_request_relnum);

                if ($searchTerm) {
                    $data = $data->where(function ($query) use ($searchTerm) {
                        $query->where('re_barcode_no', 'like', '%' . $searchTerm . '%')
                            ->orWhere('denomination', 'like', '%' . $searchTerm . '%')
                            ->orWhere('store_name', 'like', '%' . $searchTerm . '%')
                            ->orWhere('vs_date', 'like', '%' . $searchTerm . '%')
                            ->orWhere('vs_reverifydate', 'like', '%' . $searchTerm . '%')
                            ->orWhere('vs_reverifyby', 'like', '%' . $searchTerm . '%')
                            ->orWhere('vs_tf_used', 'like', '%' . $searchTerm . '%')
                            ->orWhere('vs_tf_balance', 'like', '%' . $searchTerm . '%')
                            ->orWhereRaw("CONCAT(users.firstname, ' ',users.lastname) LIKE ?", ["%{$searchTerm}%"])
                            ->orWhereRaw("CONCAT(customers.cus_fname, ' ',customers.cus_lname) LIKE ?", ["%{$searchTerm}%"]);
                    });
                }
                $data = $data->get();




                // dd($mainData->agcr_request_relnum);
                foreach ($data as $newData) {
                    // dd($newData);
                    $arr_barcodesinfo[] = [

                        'barcode' => $newData->re_barcode_no,
                        'denomination' => $newData->denomination,
                        'store' => $newData->store_name,
                        'dateverify' => $newData->vs_date,
                        'verifyby' => $newData->fullname,
                        'rdateverify' => $newData->vs_reverifydate,
                        'rverifyby' =>  $newData->vs_reverifyby,
                        'customer' => $newData->customerFullname,
                        'used' => $newData->vs_tf_used,
                        'balanced' => $newData->vs_tf_balance,
                        'type' => 'Regular GC'
                    ];
                }
            }
        }

        // elseif statement if the insp_paymentcustomer is equal to special external----------------------------

        elseif ($payment->insp_paymentcustomer == 'special external') {
            $mainData = SpecialExternalGcrequest::select(
                'special_external_gcrequest.spexgc_id',
                'special_external_gcrequest.spexgc_datereq',
                'special_external_customer.spcus_companyname',
                'special_external_gcrequest.spexgc_paymentype'
            )
                ->join('special_external_customer', 'special_external_customer.spcus_id', '=', 'special_external_gcrequest.spexgc_company')
                ->where('special_external_gcrequest.spexgc_id', $payment->insp_trid)
                ->first();

            if ($mainData) {
                $data = SpecialExternalGcrequestEmpAssign::select(
                    'special_external_gcrequest_emp_assign.spexgcemp_barcode',
                    'special_external_gcrequest_emp_assign.spexgcemp_denom',
                    'stores.store_name',
                    'store_verification.vs_date',
                    User::raw("CONCAT(users.firstname, ' ', users.lastname) as fullname"),
                    Customer::raw("CONCAT(customers.cus_fname, ' ', customers.cus_lname) as customerFullname"),
                    'store_verification.vs_tf_used',
                    'store_verification.vs_reverifydate',
                    'store_verification.vs_reverifyby',
                    'store_verification.vs_tf_balance'
                )
                    ->leftJoin('store_verification', 'store_verification.vs_barcode', '=', 'special_external_gcrequest_emp_assign.spexgcemp_barcode')
                    ->leftJoin('stores', 'stores.store_id', '=', 'store_verification.vs_store')
                    ->leftJoin('users', 'store_verification.vs_by', '=', 'users.user_id')
                    ->leftJoin('customers', 'customers.cus_id', '=', 'store_verification.vs_cn')
                    ->where('spexgcemp_trid', $payment->insp_trid);

                if ($searchTerm) {
                    $data = $data->where(function ($query) use ($searchTerm) {
                        $query->where('spexgcemp_barcode', 'like', "%{$searchTerm}%")
                            ->orWhere('spexgcemp_denom', 'like', "%{$searchTerm}%")
                            ->orWhere('store_name', 'like', "%{$searchTerm}%")
                            ->orWhere('vs_reverifydate', 'like', "%{$searchTerm}%")
                            ->orWhere('vs_reverifyby', 'like', "%{$searchTerm}%")
                            ->orWhere('vs_tf_used', 'like', "%{$searchTerm}%")
                            ->orWhere('vs_tf_balance', 'like', "%{$searchTerm}%")
                            ->orWhereRaw("CONCAT(users.firstname, ' ', users.lastname) LIKE ?", ["%{$searchTerm}%"])
                            ->orWhereRaw("CONCAT(customers.cus_fname, ' ', customers.cus_lname) LIKE ?", ["%{$searchTerm}%"]);
                    });
                }

                $data = $data->get();


                foreach ($data as $newData) {
                    $arr_barcodesinfo[] = [

                        'barcode' => $newData->spexgcemp_barcode,
                        'denomination' => $newData->spexgcemp_denom,
                        'store' => $newData->store_name,
                        'dateverify' => $newData->vs_date,
                        'verifyby' => $newData->fullname,
                        'rdateverify' => $newData->vs_reverifydate,
                        'rverifyby' => $newData->vs_reverifyby,
                        'customer' => $newData->customerFullname,
                        'used' => $newData->vs_tf_used,
                        'balanced' => $newData->vs_tf_balance,
                        'type' => 'Special External GC'
                    ];
                }
            }
        }
        // dd($arr_barcodesinfo);


        return Inertia::render('StoreAccounting/StoreAccountingViewSales', [
            'salesCustomer' => $salesCustomer,
            'data' => $arr_barcodesinfo,
            'viewSalesData' => $data,
            'search' => $searchTerm,
            'salesCustomerID' => $id

        ]);
    }


    public function viewSalesPostTransaction($barcode)
    {
        $data = StoreEodTextfileTransaction::select(
            'seodtt_line',
            'seodtt_creditlimit',
            'seodtt_credpuramt',
            'seodtt_addonamt',
            'seodtt_balance',
            'seodtt_transno',
            'seodtt_timetrnx',
            'seodtt_bu',
            'seodtt_terminalno',
            'seodtt_ackslipno',
            'seodtt_crditpurchaseamt'
        )
            ->where('seodtt_barcode', $barcode)
            ->orderBy('seodtt_id', 'ASC')
            ->get();


        return response()->json($data);
    }
    public function storeAccountingStore(Request $request)
    {
        $searchTerm = $request->input('search', '');
        $pagination = $request->input('pagination', 10);

        $storeData = TransactionStore::select(
            'transaction_stores.trans_sid',
            'transaction_stores.trans_number',
            'stores.store_name',
            DB::raw("CONCAT(store_staff.ss_firstname, ' ', store_staff.ss_lastname) as cashier"),
            DB::raw("DATE(transaction_stores.trans_datetime) as trans_date"),
            DB::raw("TIME(transaction_stores.trans_datetime) as trans_time"),
            'transaction_stores.trans_type'
        )
            ->join('stores', 'stores.store_id', '=', 'transaction_stores.trans_store')
            ->join('store_staff', 'store_staff.ss_id', '=', 'transaction_stores.trans_cashier')
            ->whereIn('trans_type', ['1', '2', '3']);

        if ($searchTerm) {
            $data = $storeData->where(function ($query) use ($searchTerm) {
                $query->where('trans_number', 'like', '%' . $searchTerm . '%')
                    ->orWhere('store_name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('trans_type', 'like', '%' . $searchTerm . '%')
                    ->orWhereRaw("DATE(transaction_stores.trans_datetime) LIKE ?", ["%{$searchTerm}%"])
                    ->orWhereRaw("TIME(transaction_stores.trans_datetime) LIKE ?", ["%{$searchTerm}%"]);
            });
        }
        $data = $storeData->orderByDesc('trans_sid')->paginate($pagination)->withQueryString();

        foreach ($data as $newData) {
            $totals = TransactionSale::select(
                DB::raw('IFNULL(COUNT(transaction_sales.sales_barcode), 0) as totalCount'),
                DB::raw('IFNULL(SUM(denomination.denomination), 0) as totalAmount')
            )
                ->join('gc', 'gc.barcode_no', '=', 'transaction_sales.sales_barcode')
                ->join('denomination', 'denomination.denom_id', '=', 'gc.denom_id')
                ->where('transaction_sales.sales_transaction_id', $newData->trans_sid)
                ->first();

            if ($totals) {
                // dd($totals);
                $newData->totalCount = $totals->totalCount ?? 0;
                // dd($newData->totalCount = $totals->totalCount ?? 0);

                $newData->totalAmount = $totals->totalAmount ?? 0;
            }
            // dd($totals);

            $newData->formatted_totalCount = number_format($newData->totalCount);
            $newData->formatted_totalAmount = number_format($newData->totalAmount, 2);

            if ($newData->trans_type == '1') {
                $newData->payment_type = 'Cash';
            } elseif ($newData->trans_type == '2') {
                $newData->payment_type = 'Credit Card';
            } elseif ($newData->trans_type == '3') {
                $newData->payment_type = 'AR';
            }
        }
        return Inertia::render('StoreAccounting/StoreAccountingStores', [
            'data' => $data,
            'search' => $searchTerm,
            'pagination' => $pagination
        ]);
    }
    public function storeAccountingViewStore(Request $request, $id)
    {
        // dd($id);
        $transNumber = $request->input('transNumber', '');
        $searchTerm = $request->input('search', '');

        $viewStoreData = TransactionStore::select(
            'transaction_stores.trans_sid',
            'transaction_stores.trans_number',
            'stores.store_name'
        )
            ->join('stores', 'stores.store_id', '=', 'transaction_stores.trans_store')
            ->where('transaction_stores.trans_sid', $id)
            ->first();

        $viewStoreSalesData = TransactionSale::select(
            'transaction_sales.sales_barcode',
            'denomination.denomination',
            'stores.store_name',
            DB::raw("CONCAT(users.firstname,' ',users.lastname) as verby"),
            DB::raw("CONCAT(customers.cus_fname,' ',customers.cus_lname) as customer"),
            'store_verification.vs_date',
            'store_verification.vs_tf_used',
            'store_verification.vs_reverifydate',
            'store_verification.vs_reverifyby',
            'store_verification.vs_tf_balance'

        )
            ->join('denomination', 'denomination.denom_id', '=', 'transaction_sales.sales_denomination')
            ->leftJoin('store_verification', 'store_verification.vs_barcode', '=', 'transaction_sales.sales_barcode')
            ->leftJoin('stores', 'stores.store_id', '=', 'store_verification.vs_store')
            ->leftJoin('users', 'store_verification.vs_by', '=', 'users.user_id')
            ->leftJoin('customers', 'customers.cus_id', '=', 'store_verification.vs_cn')
            ->where('transaction_sales.sales_transaction_id', $id);


        if ($searchTerm) {
            // dd($viewStoreData);
            $viewStoreSalesData->where(function ($query) use ($searchTerm) {
                $query->where('sales_barcode', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhere('denomination', 'like', '%' . $searchTerm . '%')
                    ->orWhere('store_name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('vs_date', 'like', '%' . $searchTerm . '%')
                    ->orWhereRaw("CONCAT(customers.cus_fname, ' ',customers.cus_lname )LIKE ?", ["%{$searchTerm}%"])
                    ->orWhereRaw("CONCAT(users.firstname, ' ',users.lastname) LIKE ?", ["%{$searchTerm}%"])
                    ->orWhere('vs_tf_balance', 'like', '%' . $searchTerm . '%');
            });
        }
        $viewStoreSalesData = $viewStoreSalesData->get();
        // ->orderByDesc('sales_id')->paginate(10)->withQueryString();	


        return Inertia::render('StoreAccounting/StoreAccountingViewStore', [
            'transnumber' => $transNumber,
            'viewStoreData' => $viewStoreData,
            'viewStoreSalesData' => $viewStoreSalesData,
            'search' => $searchTerm,
            'storeID' => $id

        ]);
    }
    public function storeAccountingViewModalStore($barcode)
    {
        // dd($barcode);
        $storeModalData = StoreEodTextfileTransaction::select(
            'seodtt_barcode',
            'seodtt_line',
            'seodtt_creditlimit',
            'seodtt_credpuramt',
            'seodtt_addonamt',
            'seodtt_balance',
            'seodtt_transno',
            'seodtt_timetrnx',
            'seodtt_bu',
            'seodtt_terminalno',
            'seodtt_ackslipno',
            'seodtt_crditpurchaseamt'
        )
            ->where('seodtt_barcode', $barcode)
            ->orderBy('seodtt_id', 'ASC')
            ->get();

        return response()->json($storeModalData);
    }

    public function storeVerifiedAlturasMall(Request $request, $id)
    {
        // dd($id);
        // dd($id);
        $pagination = $request->input('pagination', 10);
        $searchTerm = $request->input('search', '');
        // dd($searchTerm);
        $storeName = $id == 0 ? 'All Stores' : Store::where('store_id', $id)->value('store_name');

        $storeData = StoreVerification::select(
            'store_verification.vs_id',
            'store_verification.vs_barcode',
            'store_verification.vs_tf_denomination',
            DB::raw("CONCAT(customers.cus_fname, ' ', customers.cus_lname) as customer"),
            DB::raw("CONCAT(verby.firstname, ' ', verby.lastname) as verby"),
            DB::raw("CONCAT(revby.firstname, ' ', revby.lastname) as revby"),
            'store_verification.vs_tf_used',
            'store_verification.vs_tf_balance',
            DB::raw("DATE(store_verification.vs_date) as newDate"),
            'store_verification.vs_time',
            'store_verification.vs_reverifydate'
        )
            ->leftJoin('stores', 'stores.store_id', '=', 'store_verification.vs_store')
            ->leftJoin('users as revby', 'store_verification.vs_reverifyby', '=', 'revby.user_id')
            ->leftJoin('users as verby', 'store_verification.vs_by', '=', 'verby.user_id')
            ->leftJoin('customers', 'customers.cus_id', '=', 'store_verification.vs_cn')
            ->where('store_verification.vs_store', $id);
        // ->get();

        if ($searchTerm) {
            $storeData->where(function ($query) use ($searchTerm) {
                $query->where('vs_barcode', 'like', '%' . $searchTerm . '%')
                    ->orWhere('vs_tf_denomination', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhere('vs_date', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhereRaw("CONCAT(verby.firstname, ' ', verby.lastname)LIKE ?", ["%{$searchTerm}%"])
                    ->orWhereRaw("CONCAT(customers.cus_fname, ' ', customers.cus_lname)LIKE ?", ["%{$searchTerm}%"])
                    ->orWhere('vs_tf_balance', 'LIKE', '%' . $searchTerm . '%');
            });
        }
        $data = $storeData->orderByDesc('vs_id')->paginate($pagination)->withQueryString();
        // dd($data->toArray());

        return Inertia::render('StoreAccounting/AlturasMall', [
            'data' => $data,
            'id' => $id,
            'search' => $searchTerm,
            'pagination' => $pagination,
            'storeName' => $storeName

        ]);
    }

    public function alturasMallPosTransaction(Request $request, $barcode)
    {
        // dd($barcode);
        $searchQuery = $request->input('search', '');
        $alturasData = StoreEodTextfileTransaction::select(
            'seodtt_line',
            'seodtt_creditlimit',
            'seodtt_credpuramt',
            'seodtt_addonamt',
            'seodtt_balance',
            'seodtt_transno',
            'seodtt_timetrnx',
            'seodtt_bu',
            'seodtt_terminalno',
            'seodtt_ackslipno',
            'seodtt_crditpurchaseamt'
        );
        if ($searchQuery) {
            $alturasData->where(function ($query) use ($searchQuery) {
                $query->where('seodtt_line', 'like', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_creditlimit', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_credpuramt', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_addonamt', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_balance', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_transno', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_timetrnx', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_bu', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_terminalno', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_ackslipno', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_crditpurchaseamt', 'LIKE', '%' . $searchQuery . '%');
            });
        };

        $data = $alturasData->where('seodtt_barcode', $barcode)
            ->orderByDesc('seodtt_id')->paginate(10)->withQueryString();

        return Inertia::render('StoreAccounting/AlturasMallPosTransaction', [
            'data' => $data,
            'barcodeNumber' => $barcode
        ]);
    }

    public function storeVerifiedAlturasTalibon(Request $request, $id)
    {
        // dd($id);
        $pagination = $request->input('pagination', 10);
        $searchTerm = $request->input('search', '');
        // dd($searchTerm);
        $storeName = $id == 0 ? 'All Stores' : Store::where('store_id', $id)->value('store_name');
        // dd($storeName);

        $storeData = StoreVerification::select(
            'store_verification.vs_id',
            'store_verification.vs_barcode',
            'store_verification.vs_tf_denomination',
            DB::raw("CONCAT(customers.cus_fname, ' ', customers.cus_lname) as customer"),
            DB::raw("CONCAT(verby.firstname, ' ', verby.lastname) as verby"),
            DB::raw("CONCAT(revby.firstname, ' ', revby.lastname) as revby"),
            'store_verification.vs_tf_used',
            'store_verification.vs_tf_balance',
            DB::raw("DATE(store_verification.vs_date) as newDate"),
            'store_verification.vs_time',
            'store_verification.vs_reverifydate'
        )
            ->leftJoin('stores', 'stores.store_id', '=', 'store_verification.vs_store')
            ->leftJoin('users as revby', 'store_verification.vs_reverifyby', '=', 'revby.user_id')
            ->leftJoin('users as verby', 'store_verification.vs_by', '=', 'verby.user_id')
            ->leftJoin('customers', 'customers.cus_id', '=', 'store_verification.vs_cn')
            ->where('store_verification.vs_store', $id);
        // ->get();

        if ($searchTerm) {
            $storeData->where(function ($query) use ($searchTerm) {
                $query->where('vs_barcode', 'like', '%' . $searchTerm . '%')
                    ->orWhere('vs_tf_denomination', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhere('vs_date', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhereRaw("CONCAT(verby.firstname, ' ', verby.lastname)LIKE ?", ["%{$searchTerm}%"])
                    ->orWhereRaw("CONCAT(customers.cus_fname, ' ', customers.cus_lname)LIKE ?", ["%{$searchTerm}%"])
                    ->orWhere('vs_tf_balance', 'LIKE', '%' . $searchTerm . '%');
            });
        }
        $data = $storeData->orderByDesc('vs_id')->paginate($pagination)->withQueryString();
        // dd($data->toArray());

        return Inertia::render('StoreAccounting/AlturasTalibon', [
            'data' => $data,
            'id' => $id,
            'search' => $searchTerm,
            'pagination' => $pagination,
            'storeName' => $storeName

        ]);
    }

    public function talibonPosTransaction(Request $request, $barcode)
    {
        $searchQuery = $request->input('search', '');
        $alturasData = StoreEodTextfileTransaction::select(
            'seodtt_line',
            'seodtt_creditlimit',
            'seodtt_credpuramt',
            'seodtt_addonamt',
            'seodtt_balance',
            'seodtt_transno',
            'seodtt_timetrnx',
            'seodtt_bu',
            'seodtt_terminalno',
            'seodtt_ackslipno',
            'seodtt_crditpurchaseamt'
        );
        if ($searchQuery) {
            $alturasData->where(function ($query) use ($searchQuery) {
                $query->where('seodtt_line', 'like', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_creditlimit', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_credpuramt', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_addonamt', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_balance', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_transno', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_timetrnx', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_bu', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_terminalno', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_ackslipno', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_crditpurchaseamt', 'LIKE', '%' . $searchQuery . '%');
            });
        };

        $data = $alturasData->where('seodtt_barcode', $barcode)
            ->orderByDesc('seodtt_id')->paginate(10)->withQueryString();

        return Inertia::render('StoreAccounting/AlturasTalibonPosTransaction', [
            'data' => $data,
            'barcodeNumber' => $barcode
        ]);
    }

    public function storeVerifiedIslandCityMall(Request $request, $id)
    {
        // dd($id);
        $pagination = $request->input('pagination', 10);
        $searchTerm = $request->input('search', '');
        // dd($searchTerm);
        $storeName = $id == 0 ? 'All Stores' : Store::where('store_id', $id)->value('store_name');
        // dd($storeName);

        $storeData = StoreVerification::select(
            'store_verification.vs_id',
            'store_verification.vs_barcode',
            'store_verification.vs_tf_denomination',
            DB::raw("CONCAT(customers.cus_fname, ' ', customers.cus_lname) as customer"),
            DB::raw("CONCAT(verby.firstname, ' ', verby.lastname) as verby"),
            DB::raw("CONCAT(revby.firstname, ' ', revby.lastname) as revby"),
            'store_verification.vs_tf_used',
            'store_verification.vs_tf_balance',
            DB::raw("DATE(store_verification.vs_date) as newDate"),
            'store_verification.vs_time',
            'store_verification.vs_reverifydate'
        )
            ->leftJoin('stores', 'stores.store_id', '=', 'store_verification.vs_store')
            ->leftJoin('users as revby', 'store_verification.vs_reverifyby', '=', 'revby.user_id')
            ->leftJoin('users as verby', 'store_verification.vs_by', '=', 'verby.user_id')
            ->leftJoin('customers', 'customers.cus_id', '=', 'store_verification.vs_cn')
            ->where('store_verification.vs_store', $id);
        // ->get();

        if ($searchTerm) {
            $storeData->where(function ($query) use ($searchTerm) {
                $query->where('vs_barcode', 'like', '%' . $searchTerm . '%')
                    ->orWhere('vs_tf_denomination', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhere('vs_date', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhereRaw("CONCAT(verby.firstname, ' ', verby.lastname)LIKE ?", ["%{$searchTerm}%"])
                    ->orWhereRaw("CONCAT(customers.cus_fname, ' ', customers.cus_lname)LIKE ?", ["%{$searchTerm}%"])
                    ->orWhere('vs_tf_balance', 'LIKE', '%' . $searchTerm . '%');
            });
        }
        $data = $storeData->orderByDesc('vs_id')->paginate($pagination)->withQueryString();
        // dd($data->toArray());

        return Inertia::render('StoreAccounting/IslandCityMall', [
            'data' => $data,
            'id' => $id,
            'search' => $searchTerm,
            'pagination' => $pagination,
            'storeName' => $storeName

        ]);
    }
    public function islandCityMallPosTransaction(Request $request, $barcode)
    {
        $searchQuery = $request->input('search', '');
        $alturasData = StoreEodTextfileTransaction::select(
            'seodtt_line',
            'seodtt_creditlimit',
            'seodtt_credpuramt',
            'seodtt_addonamt',
            'seodtt_balance',
            'seodtt_transno',
            'seodtt_timetrnx',
            'seodtt_bu',
            'seodtt_terminalno',
            'seodtt_ackslipno',
            'seodtt_crditpurchaseamt'
        );
        if ($searchQuery) {
            $alturasData->where(function ($query) use ($searchQuery) {
                $query->where('seodtt_line', 'like', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_creditlimit', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_credpuramt', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_addonamt', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_balance', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_transno', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_timetrnx', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_bu', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_terminalno', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_ackslipno', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_crditpurchaseamt', 'LIKE', '%' . $searchQuery . '%');
            });
        };

        $data = $alturasData->where('seodtt_barcode', $barcode)
            ->orderByDesc('seodtt_id')->paginate(10)->withQueryString();

        return Inertia::render('StoreAccounting/IslandCityMallPosTransaction', [
            'data' => $data,
            'barcodeNumber' => $barcode
        ]);
    }

    public function storeVerifiedPlazaMarcela(Request $request, $id)
    {
        // dd($id);
        $pagination = $request->input('pagination', 10);
        $searchTerm = $request->input('search', '');
        // dd($searchTerm);
        $storeName = $id == 0 ? 'All Stores' : Store::where('store_id', $id)->value('store_name');
        // dd($storeName);

        $storeData = StoreVerification::select(
            'store_verification.vs_id',
            'store_verification.vs_barcode',
            'store_verification.vs_tf_denomination',
            DB::raw("CONCAT(customers.cus_fname, ' ', customers.cus_lname) as customer"),
            DB::raw("CONCAT(verby.firstname, ' ', verby.lastname) as verby"),
            DB::raw("CONCAT(revby.firstname, ' ', revby.lastname) as revby"),
            'store_verification.vs_tf_used',
            'store_verification.vs_tf_balance',
            DB::raw("DATE(store_verification.vs_date) as newDate"),
            'store_verification.vs_time',
            'store_verification.vs_reverifydate'
        )
            ->leftJoin('stores', 'stores.store_id', '=', 'store_verification.vs_store')
            ->leftJoin('users as revby', 'store_verification.vs_reverifyby', '=', 'revby.user_id')
            ->leftJoin('users as verby', 'store_verification.vs_by', '=', 'verby.user_id')
            ->leftJoin('customers', 'customers.cus_id', '=', 'store_verification.vs_cn')
            ->where('store_verification.vs_store', $id);
        // ->get();

        if ($searchTerm) {
            $storeData->where(function ($query) use ($searchTerm) {
                $query->where('vs_barcode', 'like', '%' . $searchTerm . '%')
                    ->orWhere('vs_tf_denomination', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhere('vs_date', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhereRaw("CONCAT(verby.firstname, ' ', verby.lastname)LIKE ?", ["%{$searchTerm}%"])
                    ->orWhereRaw("CONCAT(customers.cus_fname, ' ', customers.cus_lname)LIKE ?", ["%{$searchTerm}%"])
                    ->orWhere('vs_tf_balance', 'LIKE', '%' . $searchTerm . '%');
            });
        }
        $data = $storeData->orderByDesc('vs_id')->paginate($pagination)->withQueryString();
        // dd($data->toArray());

        return Inertia::render('StoreAccounting/PlazaMarcela', [
            'data' => $data,
            'id' => $id,
            'search' => $searchTerm,
            'pagination' => $pagination,
            'storeName' => $storeName

        ]);
    }

    public function plazaPostTransaction(Request $request, $barcode)
    {
        $searchQuery = $request->input('search', '');
        $alturasData = StoreEodTextfileTransaction::select(
            'seodtt_line',
            'seodtt_creditlimit',
            'seodtt_credpuramt',
            'seodtt_addonamt',
            'seodtt_balance',
            'seodtt_transno',
            'seodtt_timetrnx',
            'seodtt_bu',
            'seodtt_terminalno',
            'seodtt_ackslipno',
            'seodtt_crditpurchaseamt'
        );
        if ($searchQuery) {
            $alturasData->where(function ($query) use ($searchQuery) {
                $query->where('seodtt_line', 'like', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_creditlimit', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_credpuramt', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_addonamt', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_balance', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_transno', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_timetrnx', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_bu', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_terminalno', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_ackslipno', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_crditpurchaseamt', 'LIKE', '%' . $searchQuery . '%');
            });
        };

        $data = $alturasData->where('seodtt_barcode', $barcode)
            ->orderByDesc('seodtt_id')->paginate(10)->withQueryString();

        return Inertia::render('StoreAccounting/PlazaMarcelaPosTransaction', [
            'data' => $data,
            'barcodeNumber' => $barcode
        ]);
    }

    public function storeVerifiedAlturasTubigon(Request $request, $id)
    {
        // dd($id);
        $pagination = $request->input('pagination', 10);
        $searchTerm = $request->input('search', '');
        // dd($searchTerm);
        $storeName = $id == 0 ? 'All Stores' : Store::where('store_id', $id)->value('store_name');
        // dd($storeName);

        $storeData = StoreVerification::select(
            'store_verification.vs_id',
            'store_verification.vs_barcode',
            'store_verification.vs_tf_denomination',
            DB::raw("CONCAT(customers.cus_fname, ' ', customers.cus_lname) as customer"),
            DB::raw("CONCAT(verby.firstname, ' ', verby.lastname) as verby"),
            DB::raw("CONCAT(revby.firstname, ' ', revby.lastname) as revby"),
            'store_verification.vs_tf_used',
            'store_verification.vs_tf_balance',
            DB::raw("DATE(store_verification.vs_date) as newDate"),
            'store_verification.vs_time',
            'store_verification.vs_reverifydate'
        )
            ->leftJoin('stores', 'stores.store_id', '=', 'store_verification.vs_store')
            ->leftJoin('users as revby', 'store_verification.vs_reverifyby', '=', 'revby.user_id')
            ->leftJoin('users as verby', 'store_verification.vs_by', '=', 'verby.user_id')
            ->leftJoin('customers', 'customers.cus_id', '=', 'store_verification.vs_cn')
            ->where('store_verification.vs_store', $id);
        // ->get();

        if ($searchTerm) {
            $storeData->where(function ($query) use ($searchTerm) {
                $query->where('vs_barcode', 'like', '%' . $searchTerm . '%')
                    ->orWhere('vs_tf_denomination', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhere('vs_date', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhereRaw("CONCAT(verby.firstname, ' ', verby.lastname)LIKE ?", ["%{$searchTerm}%"])
                    ->orWhereRaw("CONCAT(customers.cus_fname, ' ', customers.cus_lname)LIKE ?", ["%{$searchTerm}%"])
                    ->orWhere('vs_tf_balance', 'LIKE', '%' . $searchTerm . '%');
            });
        }
        $data = $storeData->orderByDesc('vs_id')->paginate($pagination)->withQueryString();
        // dd($data->toArray());

        return Inertia::render('StoreAccounting/AlturasTubigon', [
            'data' => $data,
            'id' => $id,
            'search' => $searchTerm,
            'pagination' => $pagination,
            'storeName' => $storeName

        ]);
    }

    public function tubigonTransanction(Request $request, $barcode)
    {
        $searchQuery = $request->input('search', '');
        $alturasData = StoreEodTextfileTransaction::select(
            'seodtt_line',
            'seodtt_creditlimit',
            'seodtt_credpuramt',
            'seodtt_addonamt',
            'seodtt_balance',
            'seodtt_transno',
            'seodtt_timetrnx',
            'seodtt_bu',
            'seodtt_terminalno',
            'seodtt_ackslipno',
            'seodtt_crditpurchaseamt'
        );
        if ($searchQuery) {
            $alturasData->where(function ($query) use ($searchQuery) {
                $query->where('seodtt_line', 'like', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_creditlimit', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_credpuramt', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_addonamt', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_balance', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_transno', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_timetrnx', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_bu', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_terminalno', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_ackslipno', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_crditpurchaseamt', 'LIKE', '%' . $searchQuery . '%');
            });
        };

        $data = $alturasData->where('seodtt_barcode', $barcode)
            ->orderByDesc('seodtt_id')->paginate(10)->withQueryString();

        return Inertia::render('StoreAccounting/AlturasTubigonPosTransaction', [
            'data' => $data,
            'barcodeNumber' => $barcode
        ]);
    }

    public function storeVerifiedColonadeColon(Request $request, $id)
    {
        $pagination = $request->input('pagination', 10);
        $searchTerm = $request->input('search', '');
        // dd($searchTerm);
        $storeName = $id == 0 ? 'All Stores' : Store::where('store_id', $id)->value('store_name');
        // dd($storeName);

        $storeData = StoreVerification::select(
            'store_verification.vs_id',
            'store_verification.vs_barcode',
            'store_verification.vs_tf_denomination',
            DB::raw("CONCAT(customers.cus_fname, ' ', customers.cus_lname) as customer"),
            DB::raw("CONCAT(verby.firstname, ' ', verby.lastname) as verby"),
            DB::raw("CONCAT(revby.firstname, ' ', revby.lastname) as revby"),
            'store_verification.vs_tf_used',
            'store_verification.vs_tf_balance',
            DB::raw("DATE(store_verification.vs_date) as newDate"),
            'store_verification.vs_time',
            'store_verification.vs_reverifydate'
        )
            ->leftJoin('stores', 'stores.store_id', '=', 'store_verification.vs_store')
            ->leftJoin('users as revby', 'store_verification.vs_reverifyby', '=', 'revby.user_id')
            ->leftJoin('users as verby', 'store_verification.vs_by', '=', 'verby.user_id')
            ->leftJoin('customers', 'customers.cus_id', '=', 'store_verification.vs_cn')
            ->where('store_verification.vs_store', $id);
        // ->get();

        if ($searchTerm) {
            $storeData->where(function ($query) use ($searchTerm) {
                $query->where('vs_barcode', 'like', '%' . $searchTerm . '%')
                    ->orWhere('vs_tf_denomination', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhere('vs_date', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhereRaw("CONCAT(verby.firstname, ' ', verby.lastname)LIKE ?", ["%{$searchTerm}%"])
                    ->orWhereRaw("CONCAT(customers.cus_fname, ' ', customers.cus_lname)LIKE ?", ["%{$searchTerm}%"])
                    ->orWhere('vs_tf_balance', 'LIKE', '%' . $searchTerm . '%');
            });
        }
        $data = $storeData->orderByDesc('vs_id')->paginate($pagination)->withQueryString();
        // dd($data->toArray());

        return Inertia::render('StoreAccounting/ColonadeColon', [
            'data' => $data,
            'id' => $id,
            'search' => $searchTerm,
            'pagination' => $pagination,
            'storeName' => $storeName

        ]);
    }

    public function transactionColonadeColon(Request $request, $barcode)
    {
        $searchQuery = $request->input('search', '');
        $alturasData = StoreEodTextfileTransaction::select(
            'seodtt_line',
            'seodtt_creditlimit',
            'seodtt_credpuramt',
            'seodtt_addonamt',
            'seodtt_balance',
            'seodtt_transno',
            'seodtt_timetrnx',
            'seodtt_bu',
            'seodtt_terminalno',
            'seodtt_ackslipno',
            'seodtt_crditpurchaseamt'
        );
        if ($searchQuery) {
            $alturasData->where(function ($query) use ($searchQuery) {
                $query->where('seodtt_line', 'like', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_creditlimit', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_credpuramt', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_addonamt', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_balance', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_transno', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_timetrnx', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_bu', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_terminalno', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_ackslipno', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_crditpurchaseamt', 'LIKE', '%' . $searchQuery . '%');
            });
        };

        $data = $alturasData->where('seodtt_barcode', $barcode)
            ->orderByDesc('seodtt_id')->paginate(10)->withQueryString();

        return Inertia::render('StoreAccounting/colonadeColonPosTransaction', [
            'data' => $data,
            'barcodeNumber' => $barcode
        ]);
    }

    public function storeVerifiedColonadeMandaue(Request $request, $id)
    {
        $pagination = $request->input('pagination', 10);
        $searchTerm = $request->input('search', '');
        // dd($searchTerm);
        $storeName = $id == 0 ? 'All Stores' : Store::where('store_id', $id)->value('store_name');
        // dd($storeName);

        $storeData = StoreVerification::select(
            'store_verification.vs_id',
            'store_verification.vs_barcode',
            'store_verification.vs_tf_denomination',
            DB::raw("CONCAT(customers.cus_fname, ' ', customers.cus_lname) as customer"),
            DB::raw("CONCAT(verby.firstname, ' ', verby.lastname) as verby"),
            DB::raw("CONCAT(revby.firstname, ' ', revby.lastname) as revby"),
            'store_verification.vs_tf_used',
            'store_verification.vs_tf_balance',
            DB::raw("DATE(store_verification.vs_date) as newDate"),
            'store_verification.vs_time',
            'store_verification.vs_reverifydate'
        )
            ->leftJoin('stores', 'stores.store_id', '=', 'store_verification.vs_store')
            ->leftJoin('users as revby', 'store_verification.vs_reverifyby', '=', 'revby.user_id')
            ->leftJoin('users as verby', 'store_verification.vs_by', '=', 'verby.user_id')
            ->leftJoin('customers', 'customers.cus_id', '=', 'store_verification.vs_cn')
            ->where('store_verification.vs_store', $id);
        // ->get();

        if ($searchTerm) {
            $storeData->where(function ($query) use ($searchTerm) {
                $query->where('vs_barcode', 'like', '%' . $searchTerm . '%')
                    ->orWhere('vs_tf_denomination', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhere('vs_date', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhereRaw("CONCAT(verby.firstname, ' ', verby.lastname)LIKE ?", ["%{$searchTerm}%"])
                    ->orWhereRaw("CONCAT(customers.cus_fname, ' ', customers.cus_lname)LIKE ?", ["%{$searchTerm}%"])
                    ->orWhere('vs_tf_balance', 'LIKE', '%' . $searchTerm . '%');
            });
        }
        $data = $storeData->orderByDesc('vs_id')->paginate($pagination)->withQueryString();
        // dd($data->toArray());

        return Inertia::render('StoreAccounting/ColonadeMandaue', [
            'data' => $data,
            'id' => $id,
            'search' => $searchTerm,
            'pagination' => $pagination,
            'storeName' => $storeName

        ]);
    }

    public function transactionColonadeMandaue(Request $request, $barcode)
    {
        $searchQuery = $request->input('search', '');
        $alturasData = StoreEodTextfileTransaction::select(
            'seodtt_line',
            'seodtt_creditlimit',
            'seodtt_credpuramt',
            'seodtt_addonamt',
            'seodtt_balance',
            'seodtt_transno',
            'seodtt_timetrnx',
            'seodtt_bu',
            'seodtt_terminalno',
            'seodtt_ackslipno',
            'seodtt_crditpurchaseamt'
        );
        if ($searchQuery) {
            $alturasData->where(function ($query) use ($searchQuery) {
                $query->where('seodtt_line', 'like', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_creditlimit', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_credpuramt', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_addonamt', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_balance', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_transno', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_timetrnx', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_bu', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_terminalno', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_ackslipno', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_crditpurchaseamt', 'LIKE', '%' . $searchQuery . '%');
            });
        };

        $data = $alturasData->where('seodtt_barcode', $barcode)
            ->orderByDesc('seodtt_id')->paginate(10)->withQueryString();

        return Inertia::render('StoreAccounting/ColonadeMandauePosTransaction', [
            'data' => $data,
            'barcodeNumber' => $barcode
        ]);
    }

    public function storeVerifiedAltaCitta(Request $request, $id)
    {
        $pagination = $request->input('pagination', 10);
        $searchTerm = $request->input('search', '');
        // dd($searchTerm);
        $storeName = $id == 0 ? 'All Stores' : Store::where('store_id', $id)->value('store_name');
        // dd($storeName);

        $storeData = StoreVerification::select(
            'store_verification.vs_id',
            'store_verification.vs_barcode',
            'store_verification.vs_tf_denomination',
            DB::raw("CONCAT(customers.cus_fname, ' ', customers.cus_lname) as customer"),
            DB::raw("CONCAT(verby.firstname, ' ', verby.lastname) as verby"),
            DB::raw("CONCAT(revby.firstname, ' ', revby.lastname) as revby"),
            'store_verification.vs_tf_used',
            'store_verification.vs_tf_balance',
            DB::raw("DATE(store_verification.vs_date) as newDate"),
            'store_verification.vs_time',
            'store_verification.vs_reverifydate'
        )
            ->leftJoin('stores', 'stores.store_id', '=', 'store_verification.vs_store')
            ->leftJoin('users as revby', 'store_verification.vs_reverifyby', '=', 'revby.user_id')
            ->leftJoin('users as verby', 'store_verification.vs_by', '=', 'verby.user_id')
            ->leftJoin('customers', 'customers.cus_id', '=', 'store_verification.vs_cn')
            ->where('store_verification.vs_store', $id);
        // ->get();

        if ($searchTerm) {
            $storeData->where(function ($query) use ($searchTerm) {
                $query->where('vs_barcode', 'like', '%' . $searchTerm . '%')
                    ->orWhere('vs_tf_denomination', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhere('vs_date', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhereRaw("CONCAT(verby.firstname, ' ', verby.lastname)LIKE ?", ["%{$searchTerm}%"])
                    ->orWhereRaw("CONCAT(customers.cus_fname, ' ', customers.cus_lname)LIKE ?", ["%{$searchTerm}%"])
                    ->orWhere('vs_tf_balance', 'LIKE', '%' . $searchTerm . '%');
            });
        }
        $data = $storeData->orderByDesc('vs_id')->paginate($pagination)->withQueryString();
        // dd($data->toArray());

        return Inertia::render('StoreAccounting/AltaCitta', [
            'data' => $data,
            'id' => $id,
            'search' => $searchTerm,
            'pagination' => $pagination,
            'storeName' => $storeName

        ]);
    }

    public function transactionAltaCitta(Request $request, $barcode)
    {
        $searchQuery = $request->input('search', '');
        $alturasData = StoreEodTextfileTransaction::select(
            'seodtt_line',
            'seodtt_creditlimit',
            'seodtt_credpuramt',
            'seodtt_addonamt',
            'seodtt_balance',
            'seodtt_transno',
            'seodtt_timetrnx',
            'seodtt_bu',
            'seodtt_terminalno',
            'seodtt_ackslipno',
            'seodtt_crditpurchaseamt'
        );
        if ($searchQuery) {
            $alturasData->where(function ($query) use ($searchQuery) {
                $query->where('seodtt_line', 'like', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_creditlimit', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_credpuramt', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_addonamt', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_balance', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_transno', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_timetrnx', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_bu', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_terminalno', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_ackslipno', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_crditpurchaseamt', 'LIKE', '%' . $searchQuery . '%');
            });
        };

        $data = $alturasData->where('seodtt_barcode', $barcode)
            ->orderByDesc('seodtt_id')->paginate(10)->withQueryString();

        return Inertia::render('StoreAccounting/AltaCittaPosTransaction', [
            'data' => $data,
            'barcodeNumber' => $barcode
        ]);
    }

    public function storeVerifiedFarmersMarket(Request $request, $id)
    {
        $pagination = $request->input('pagination', 10);
        $searchTerm = $request->input('search', '');
        // dd($searchTerm);
        $storeName = $id == 0 ? 'All Stores' : Store::where('store_id', $id)->value('store_name');
        // dd($storeName);

        $storeData = StoreVerification::select(
            'store_verification.vs_id',
            'store_verification.vs_barcode',
            'store_verification.vs_tf_denomination',
            DB::raw("CONCAT(customers.cus_fname, ' ', customers.cus_lname) as customer"),
            DB::raw("CONCAT(verby.firstname, ' ', verby.lastname) as verby"),
            DB::raw("CONCAT(revby.firstname, ' ', revby.lastname) as revby"),
            'store_verification.vs_tf_used',
            'store_verification.vs_tf_balance',
            DB::raw("DATE(store_verification.vs_date) as newDate"),
            'store_verification.vs_time',
            'store_verification.vs_reverifydate'
        )
            ->leftJoin('stores', 'stores.store_id', '=', 'store_verification.vs_store')
            ->leftJoin('users as revby', 'store_verification.vs_reverifyby', '=', 'revby.user_id')
            ->leftJoin('users as verby', 'store_verification.vs_by', '=', 'verby.user_id')
            ->leftJoin('customers', 'customers.cus_id', '=', 'store_verification.vs_cn')
            ->where('store_verification.vs_store', $id);
        // ->get();

        if ($searchTerm) {
            $storeData->where(function ($query) use ($searchTerm) {
                $query->where('vs_barcode', 'like', '%' . $searchTerm . '%')
                    ->orWhere('vs_tf_denomination', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhere('vs_date', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhereRaw("CONCAT(verby.firstname, ' ', verby.lastname)LIKE ?", ["%{$searchTerm}%"])
                    ->orWhereRaw("CONCAT(customers.cus_fname, ' ', customers.cus_lname)LIKE ?", ["%{$searchTerm}%"])
                    ->orWhere('vs_tf_balance', 'LIKE', '%' . $searchTerm . '%');
            });
        }
        $data = $storeData->orderByDesc('vs_id')->paginate($pagination)->withQueryString();
        // dd($data->toArray());

        return Inertia::render('StoreAccounting/FarmersMarket', [
            'data' => $data,
            'id' => $id,
            'search' => $searchTerm,
            'pagination' => $pagination,
            'storeName' => $storeName

        ]);
    }

    public function transactionFarmersMarket(Request $request, $barcode)
    {
        $searchQuery = $request->input('search', '');
        $alturasData = StoreEodTextfileTransaction::select(
            'seodtt_line',
            'seodtt_creditlimit',
            'seodtt_credpuramt',
            'seodtt_addonamt',
            'seodtt_balance',
            'seodtt_transno',
            'seodtt_timetrnx',
            'seodtt_bu',
            'seodtt_terminalno',
            'seodtt_ackslipno',
            'seodtt_crditpurchaseamt'
        );
        if ($searchQuery) {
            $alturasData->where(function ($query) use ($searchQuery) {
                $query->where('seodtt_line', 'like', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_creditlimit', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_credpuramt', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_addonamt', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_balance', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_transno', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_timetrnx', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_bu', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_terminalno', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_ackslipno', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_crditpurchaseamt', 'LIKE', '%' . $searchQuery . '%');
            });
        };

        $data = $alturasData->where('seodtt_barcode', $barcode)
            ->orderByDesc('seodtt_id')->paginate(10)->withQueryString();

        return Inertia::render('StoreAccounting/FarmersMarketPosTransaction', [
            'data' => $data,
            'barcodeNumber' => $barcode
        ]);
    }

    public function storeVerifiedUbayDistribution(Request $request, $id)
    {
        $pagination = $request->input('pagination', 10);
        $searchTerm = $request->input('search', '');
        // dd($searchTerm);
        $storeName = $id == 0 ? 'All Stores' : Store::where('store_id', $id)->value('store_name');
        // dd($storeName);

        $storeData = StoreVerification::select(
            'store_verification.vs_id',
            'store_verification.vs_barcode',
            'store_verification.vs_tf_denomination',
            DB::raw("CONCAT(customers.cus_fname, ' ', customers.cus_lname) as customer"),
            DB::raw("CONCAT(verby.firstname, ' ', verby.lastname) as verby"),
            DB::raw("CONCAT(revby.firstname, ' ', revby.lastname) as revby"),
            'store_verification.vs_tf_used',
            'store_verification.vs_tf_balance',
            DB::raw("DATE(store_verification.vs_date) as newDate"),
            'store_verification.vs_time',
            'store_verification.vs_reverifydate'
        )
            ->leftJoin('stores', 'stores.store_id', '=', 'store_verification.vs_store')
            ->leftJoin('users as revby', 'store_verification.vs_reverifyby', '=', 'revby.user_id')
            ->leftJoin('users as verby', 'store_verification.vs_by', '=', 'verby.user_id')
            ->leftJoin('customers', 'customers.cus_id', '=', 'store_verification.vs_cn')
            ->where('store_verification.vs_store', $id);
        // ->get();

        if ($searchTerm) {
            $storeData->where(function ($query) use ($searchTerm) {
                $query->where('vs_barcode', 'like', '%' . $searchTerm . '%')
                    ->orWhere('vs_tf_denomination', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhereRaw("CONCAT(customers.cus_fname, ' ', customers.cus_lname)LIKE ?", ["%{$searchTerm}%"])
                    ->orWhere('vs_date', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhereRaw("CONCAT(verby.firstname, ' ', verby.lastname)LIKE ?", ["%{$searchTerm}%"])
                    ->orWhere('vs_tf_balance', 'LIKE', '%' . $searchTerm . '%');
            });
        }
        $data = $storeData->orderByDesc('vs_id')->paginate($pagination)->withQueryString();
        // dd($data->toArray());

        return Inertia::render('StoreAccounting/UbayDistribution', [
            'data' => $data,
            'id' => $id,
            'search' => $searchTerm,
            'pagination' => $pagination,
            'storeName' => $storeName

        ]);
    }

    public function transactionUbayDistribution(Request $request, $barcode)
    {
        $searchQuery = $request->input('search', '');
        $alturasData = StoreEodTextfileTransaction::select(
            'seodtt_line',
            'seodtt_creditlimit',
            'seodtt_credpuramt',
            'seodtt_addonamt',
            'seodtt_balance',
            'seodtt_transno',
            'seodtt_timetrnx',
            'seodtt_bu',
            'seodtt_terminalno',
            'seodtt_ackslipno',
            'seodtt_crditpurchaseamt'
        );
        if ($searchQuery) {
            $alturasData->where(function ($query) use ($searchQuery) {
                $query->where('seodtt_line', 'like', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_creditlimit', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_credpuramt', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_addonamt', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_balance', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_transno', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_timetrnx', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_bu', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_terminalno', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_ackslipno', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_crditpurchaseamt', 'LIKE', '%' . $searchQuery . '%');
            });
        };

        $data = $alturasData->where('seodtt_barcode', $barcode)
            ->orderByDesc('seodtt_id')->paginate(10)->withQueryString();

        return Inertia::render('StoreAccounting/UbayDistributionPosTransaction', [
            'data' => $data,
            'barcodeNumber' => $barcode
        ]);
    }

    public function storeVerifiedScreenville(Request $request, $id)
    {
        $pagination = $request->input('pagination', 10);
        $searchTerm = $request->input('search', '');
        // dd($searchTerm);
        $storeName = $id == 0 ? 'All Stores' : Store::where('store_id', $id)->value('store_name');
        // dd($storeName);

        $storeData = StoreVerification::select(
            'store_verification.vs_id',
            'store_verification.vs_barcode',
            'store_verification.vs_tf_denomination',
            DB::raw("CONCAT(customers.cus_fname, ' ', customers.cus_lname) as customer"),
            DB::raw("CONCAT(verby.firstname, ' ', verby.lastname) as verby"),
            DB::raw("CONCAT(revby.firstname, ' ', revby.lastname) as revby"),
            'store_verification.vs_tf_used',
            'store_verification.vs_tf_balance',
            DB::raw("DATE(store_verification.vs_date) as newDate"),
            'store_verification.vs_time',
            'store_verification.vs_reverifydate'
        )
            ->leftJoin('stores', 'stores.store_id', '=', 'store_verification.vs_store')
            ->leftJoin('users as revby', 'store_verification.vs_reverifyby', '=', 'revby.user_id')
            ->leftJoin('users as verby', 'store_verification.vs_by', '=', 'verby.user_id')
            ->leftJoin('customers', 'customers.cus_id', '=', 'store_verification.vs_cn')
            ->where('store_verification.vs_store', $id);
        // ->get();

        if ($searchTerm) {
            $storeData->where(function ($query) use ($searchTerm) {
                $query->where('vs_barcode', 'like', '%' . $searchTerm . '%')
                    ->orWhere('vs_tf_denomination', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhereRaw("CONCAT(customers.cus_fname, ' ', customers.cus_lname)LIKE ?", ["%{$searchTerm}%"])
                    ->orWhere('vs_date', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhereRaw("CONCAT(verby.firstname, ' ', verby.lastname)LIKE ?", ["%{$searchTerm}%"])
                    ->orWhere('vs_tf_balance', 'LIKE', '%' . $searchTerm . '%');
            });
        }
        $data = $storeData->orderByDesc('vs_id')->paginate($pagination)->withQueryString();
        // dd($data->toArray());

        return Inertia::render('StoreAccounting/Screenville', [
            'data' => $data,
            'id' => $id,
            'search' => $searchTerm,
            'pagination' => $pagination,
            'storeName' => $storeName

        ]);
    }

    public function transactionScreenville(Request $request, $barcode)
    {
        $searchQuery = $request->input('search', '');
        $alturasData = StoreEodTextfileTransaction::select(
            'seodtt_line',
            'seodtt_creditlimit',
            'seodtt_credpuramt',
            'seodtt_addonamt',
            'seodtt_balance',
            'seodtt_transno',
            'seodtt_timetrnx',
            'seodtt_bu',
            'seodtt_terminalno',
            'seodtt_ackslipno',
            'seodtt_crditpurchaseamt'
        );
        if ($searchQuery) {
            $alturasData->where(function ($query) use ($searchQuery) {
                $query->where('seodtt_line', 'like', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_creditlimit', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_credpuramt', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_addonamt', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_balance', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_transno', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_timetrnx', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_bu', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_terminalno', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_ackslipno', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_crditpurchaseamt', 'LIKE', '%' . $searchQuery . '%');
            });
        };

        $data = $alturasData->where('seodtt_barcode', $barcode)
            ->orderByDesc('seodtt_id')->paginate(10)->withQueryString();

        return Inertia::render('StoreAccounting/ScreenvillePosTransaction', [
            'data' => $data,
            'barcodeNumber' => $barcode
        ]);
    }

    public function storeVerifiedAscTech(Request $request, $id)
    {
        $pagination = $request->input('pagination', 10);
        $searchTerm = $request->input('search', '');
        // dd($searchTerm);
        $storeName = $id == 0 ? 'All Stores' : Store::where('store_id', $id)->value('store_name');
        // dd($storeName);

        $storeData = StoreVerification::select(
            'store_verification.vs_id',
            'store_verification.vs_barcode',
            'store_verification.vs_tf_denomination',
            DB::raw("CONCAT(customers.cus_fname, ' ', customers.cus_lname) as customer"),
            DB::raw("CONCAT(verby.firstname, ' ', verby.lastname) as verby"),
            DB::raw("CONCAT(revby.firstname, ' ', revby.lastname) as revby"),
            'store_verification.vs_tf_used',
            'store_verification.vs_tf_balance',
            DB::raw("DATE(store_verification.vs_date) as newDate"),
            'store_verification.vs_time',
            'store_verification.vs_reverifydate'
        )
            ->leftJoin('stores', 'stores.store_id', '=', 'store_verification.vs_store')
            ->leftJoin('users as revby', 'store_verification.vs_reverifyby', '=', 'revby.user_id')
            ->leftJoin('users as verby', 'store_verification.vs_by', '=', 'verby.user_id')
            ->leftJoin('customers', 'customers.cus_id', '=', 'store_verification.vs_cn')
            ->where('store_verification.vs_store', $id);
        // ->get();

        if ($searchTerm) {
            $storeData->where(function ($query) use ($searchTerm) {
                $query->where('vs_barcode', 'like', '%' . $searchTerm . '%')
                    ->orWhere('vs_tf_denomination', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhereRaw("CONCAT(customers.cus_fname, ' ', customers.cus_lname)LIKE ?", ["%{$searchTerm}%"])
                    ->orWhere('vs_date', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhereRaw("CONCAT(verby.firstname, ' ', verby.lastname)LIKE ?", ["%{$searchTerm}%"])
                    ->orWhere('vs_tf_balance', 'LIKE', '%' . $searchTerm . '%');
            });
        }
        $data = $storeData->orderByDesc('vs_id')->paginate($pagination)->withQueryString();
        // dd($searchTerm);

        return Inertia::render('StoreAccounting/AscTech', [
            'data' => $data,
            'id' => $id,
            'search' => $searchTerm,
            'pagination' => $pagination,
            'storeName' => $storeName

        ]);
    }

    public function transactionAscTech(Request $request, $barcode)
    {
        $searchQuery = $request->input('search', '');
        $alturasData = StoreEodTextfileTransaction::select(
            'seodtt_line',
            'seodtt_creditlimit',
            'seodtt_credpuramt',
            'seodtt_addonamt',
            'seodtt_balance',
            'seodtt_transno',
            'seodtt_timetrnx',
            'seodtt_bu',
            'seodtt_terminalno',
            'seodtt_ackslipno',
            'seodtt_crditpurchaseamt'
        );
        if ($searchQuery) {
            $alturasData->where(function ($query) use ($searchQuery) {
                $query->where('seodtt_line', 'like', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_creditlimit', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_credpuramt', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_addonamt', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_balance', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_transno', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_timetrnx', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_bu', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_terminalno', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_ackslipno', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('seodtt_crditpurchaseamt', 'LIKE', '%' . $searchQuery . '%');
            });
        };

        $data = $alturasData->where('seodtt_barcode', $barcode)
            ->orderByDesc('seodtt_id')->paginate(10)->withQueryString();

        return Inertia::render('StoreAccounting/ScreenvillePosTransaction', [
            'data' => $data,
            'barcodeNumber' => $barcode
        ]);
    }
    public function verifiedGCReport()
    {
        return Inertia::render('StoreAccounting/VerifiedGCReport');
    }

    public function verifiedGcSubmit(Request $request)
    {
       
        dd($request->toArray());
    }

    public function verifiedGcYearlySubmit(Request $request)
    {
        dd($request->toArray());
    }
    public function storeGCPurchasedReport()
    {
        return Inertia::render('StoreAccounting/StoreGCPurchased');
    }
    
    public function billingMonthlySubmit(Request $request){
        dd($request->toArray());
    }

    public function billingYearlySubmit(Request $request){
        dd($request->toArray());
    }
    public function redeemReport()
    {
        return Inertia::render('StoreAccounting/SPGCRedeemReport');
    }

    public function monthlyRedeemSubmit(Request $request){
        dd($request->toArray());
    }

    public function yearlyRedeemSubmit(Request $request){
        dd($request->toArray());
    }

    public function verifiedStore()
    {
        return Inertia::render('StoreAccounting/VerifiedStore');
    }

    public function puchasedMonthlySubmit (Request $request){
        dd($request->toArray());
    }

    public function purchasedYearlySubmit (Request $request){
        dd($request->toArray());
    }

    public function SPGCApproved(Request $request)
    {

        return Inertia::render('StoreAccounting/SPGC_Approved');
    }

    public function SPGCApprovedSubmit(Request $request)
    {

        $startDateData = $request->input('spgcStartDate');
        $endDateData = $request->input('spgcEndDate');

        if ($startDateData && $endDateData) {
            $formattedStartDate = Carbon::createFromFormat('Y-m-d', $startDateData)->format('F d, Y');
            $formattedEndDate = Carbon::createFromFormat('Y-m-d', $endDateData)->format('F d, Y');
            $selectedDate = [
                'start' => $formattedStartDate,
                'end' => $formattedEndDate
            ];
            $fromDate = "{$selectedDate['start']} ";
            $toDate = "{$selectedDate['end']} ";
        }


        $datacus1 = DB::table('special_external_gcrequest_emp_assign')
            ->join('special_external_gcrequest', 'special_external_gcrequest.spexgc_id', '=', 'special_external_gcrequest_emp_assign.spexgcemp_trid')
            ->join('approved_request', 'approved_request.reqap_trid', '=', 'special_external_gcrequest.spexgc_id')
            ->join('users as reqby', 'reqby.user_id', '=', 'special_external_gcrequest.spexgc_reqby')
            ->join('special_external_customer', 'special_external_customer.spcus_id', '=', 'special_external_gcrequest.spexgc_company')
            ->where('approved_request.reqap_approvedtype', 'Special External GC Approved')
            ->where('special_external_gcrequest_emp_assign.spexgc_status', '!=', 'inactive')
            ->whereBetween(DB::raw("DATE_FORMAT(approved_request.reqap_date, '%Y-%m-%d')"), [$startDateData, $endDateData])
            ->select(
                DB::raw("IFNULL(SUM(special_external_gcrequest_emp_assign.spexgcemp_denom), 0.00) as totdenom"),
                DB::raw("IFNULL(COUNT(special_external_gcrequest_emp_assign.spexgcemp_barcode), 0) as totcnt"),
                'special_external_gcrequest.spexgc_num',
                DB::raw("DATE_FORMAT(special_external_gcrequest.spexgc_datereq, '%m/%d/%Y') as datereq"),
                DB::raw("DATE_FORMAT(approved_request.reqap_date, '%m/%d/%Y') as daterel"),
                DB::raw("CONCAT(reqby.firstname, ' ', reqby.lastname) as trby"),
                'special_external_customer.spcus_acctname',
                'special_external_customer.spcus_companyname'
            )
            ->groupBy(
                'special_external_gcrequest.spexgc_num',
                'special_external_gcrequest.spexgc_datereq',
                'approved_request.reqap_date',
                'reqby.firstname',
                'reqby.lastname',
                'special_external_customer.spcus_acctname',
                'special_external_customer.spcus_companyname'
            )

            ->orderBy('special_external_gcrequest.spexgc_datereq')
            ->get();

        // $searchData = $request->input('search', '');
        // // dd($searchData);

        // if ($searchData) {
        //     $datacus1 = $datacus1->where(function ($query) use ($searchData) {
        //         if (!empty($searchData['companyName'])) {
        //             $query->where('special_external_customer.spcus_companyname', 'like', '%' . $searchData['companyName'] . '%');
        //         }

        //         if (!empty($searchData['startDate']) && !empty($searchData['endDate'])) {
        //             $query->whereBetween('special_external_gcrequest.spexgc_datereq', [$searchData['startDate'], $searchData['endDate']]);
        //         } elseif (!empty($searchData['startDate'])) {
        //             $query->where('special_external_gcrequest.spexgc_datereq', '>=', $searchData['startDate']);
        //         } elseif (!empty($searchData['endDate'])) {
        //             $query->where('special_external_gcrequest.spexgc_datereq', '<=', $searchData['endDate']);
        //         }
        //     });
        // }

        // Add ordering and retrieve the results




        $databar1 = DB::table('special_external_gcrequest_emp_assign')
            ->join('special_external_gcrequest', 'special_external_gcrequest.spexgc_id', '=', 'special_external_gcrequest_emp_assign.spexgcemp_trid')
            ->join('approved_request', 'approved_request.reqap_trid', '=', 'special_external_gcrequest.spexgc_id')
            ->where('approved_request.reqap_approvedtype', 'Special External GC Approved')
            ->where('special_external_gcrequest_emp_assign.spexgc_status', '!=', 'inactive')
            ->whereBetween(DB::raw("DATE_FORMAT(approved_request.reqap_date, '%Y-%m-%d')"), [$startDateData, $endDateData])
            ->select(
                'special_external_gcrequest_emp_assign.spexgcemp_denom',
                DB::raw("CONCAT(special_external_gcrequest_emp_assign.spexgcemp_fname,
                 ' ',special_external_gcrequest_emp_assign.spexgcemp_mname,
                 ' ',special_external_gcrequest_emp_assign.spexgcemp_lname ) AS customer_name"),
                'special_external_gcrequest_emp_assign.voucher',
                'special_external_gcrequest_emp_assign.spexgcemp_extname',
                'special_external_gcrequest_emp_assign.spexgcemp_barcode',
                'special_external_gcrequest.spexgc_num',
                DB::raw("DATE_FORMAT(special_external_gcrequest.spexgc_datereq, '%m/%d/%Y') as datereq"),
                DB::raw("DATE_FORMAT(approved_request.reqap_date, '%m/%d/%Y') as daterel")
            )
            ->orderBy('special_external_gcrequest_emp_assign.spexgcemp_barcode', 'asc')
            ->get();

        return Inertia::render('StoreAccounting/SPGC_Approved', [
            'dataCustomer' => $datacus1,
            'dataBarcode' => $databar1,
            'fromDate' => $fromDate,
            'toDate' => $toDate

        ]);
    }

    // public function ExcelApprovedSubmit(Request $request)
    // {
    //     // dd();
    //     $startDateData = $request->input('spgcStartDate');
    //     $endDateData = $request->input('spgcEndDate');

    //     if ($startDateData && $endDateData) {
    //         $formattedStartDate = Carbon::createFromFormat('Y-m-d', $startDateData)->format('F d, Y');
    //         $formattedEndDate = Carbon::createFromFormat('Y-m-d', $endDateData)->format('F d, Y');
    //         $selectedDate = [
    //             'start' => $formattedStartDate,
    //             'end' => $formattedEndDate
    //         ];
    //         $finalDateSelected = "From: {$selectedDate['start']} To: {$selectedDate['end']}";
    //     }

    //     $data1 = DB::table('special_external_gcrequest_emp_assign')
    //         ->join('special_external_gcrequest', 'special_external_gcrequest.spexgc_id', '=', 'special_external_gcrequest_emp_assign.spexgcemp_trid')
    //         ->join('approved_request', 'approved_request.reqap_trid', '=', 'special_external_gcrequest.spexgc_id')
    //         ->where('approved_request.reqap_approvedtype', 'Special External GC Approved')
    //         ->where('special_external_gcrequest_emp_assign.spexgc_status', '!=', 'inactive')
    //         ->whereBetween(DB::raw("DATE_FORMAT(approved_request.reqap_date, '%m/%d/%Y')"), [$startDateData, $endDateData])
    //         ->select(
    //             'special_external_gcrequest_emp_assign.spexgcemp_denom',
    //             'special_external_gcrequest_emp_assign.spexgcemp_fname',
    //             'special_external_gcrequest_emp_assign.spexgcemp_lname',
    //             'special_external_gcrequest_emp_assign.spexgcemp_mname',
    //             'special_external_gcrequest_emp_assign.spexgcemp_extname',
    //             'special_external_gcrequest_emp_assign.spexgcemp_barcode',
    //             'special_external_gcrequest_emp_assign.voucher',
    //             'special_external_gcrequest.spexgc_num'
    //         )
    //         ->orderBy('special_external_gcrequest.spexgc_datereq', 'ASC')
    //         ->get();

    //     $data2 = DB::table('special_external_gcrequest')
    //         ->join('approved_request', 'approved_request.reqap_trid', '=', 'special_external_gcrequest.spexgc_id')
    //         ->join('users as reqby', 'reqby.user_id', '=', 'special_external_gcrequest.spexgc_reqby')
    //         ->where('approved_request.reqap_approvedtype', 'Special External GC Approved')
    //         ->get();


    //     return Inertia::render('StoreAccounting/SPGC_Approved', [
    //         'data1' => $data1,
    //         'data2' => $data2,
    //         'finalDateSelectedExcel' => $finalDateSelected
    //     ]);
    // }

    public function SPGCRelease()
    {
        return Inertia::render('StoreAccounting/SPGC_Release');
    }


    public function SPGCReleasedSubmit(Request $request)
    {
        $startDateData = $request->input('startDate');
        $endDateData = $request->input('endDate');

        if ($startDateData && $endDateData) {
            $formattedStartDate = Carbon::createFromFormat('Y-m-d', $startDateData)->format('F d, Y');
            $formattedEndDate = Carbon::createFromFormat('Y-m-d', $endDateData)->format('F d, Y');
            $selectedDate = [
                'start' => $formattedStartDate,
                'end' => $formattedEndDate
            ];
            $fromDate = "{$selectedDate['start']}";
            $toDate = "{$selectedDate['end']}";
        }

        $datacus1 = DB::table('special_external_gcrequest_emp_assign')
            ->join('special_external_gcrequest', 'special_external_gcrequest.spexgc_id', '=', 'special_external_gcrequest_emp_assign.spexgcemp_trid')
            ->join('approved_request', 'approved_request.reqap_trid', '=', 'special_external_gcrequest.spexgc_id')
            ->join('users as reqby', 'reqby.user_id', '=', 'special_external_gcrequest.spexgc_reqby')
            ->join('special_external_customer', 'special_external_customer.spcus_id', '=', 'special_external_gcrequest.spexgc_company')
            ->where('approved_request.reqap_approvedtype', 'Special External GC Approved')
            ->where('special_external_gcrequest_emp_assign.spexgc_status', '!=', 'inactive')
            ->whereBetween(DB::raw("DATE_FORMAT(approved_request.reqap_date, '%Y-%m-%d')"), [$startDateData, $endDateData])
            ->select(
                DB::raw("IFNULL(SUM(special_external_gcrequest_emp_assign.spexgcemp_denom), 0.00) as totdenom"),
                DB::raw("IFNULL(COUNT(special_external_gcrequest_emp_assign.spexgcemp_barcode), 0) as totcnt"),
                'special_external_gcrequest.spexgc_num',
                DB::raw("DATE_FORMAT(special_external_gcrequest.spexgc_datereq, '%m/%d/%Y') as datereq"),
                DB::raw("DATE_FORMAT(approved_request.reqap_date, '%m/%d/%Y') as daterel"),
                DB::raw("CONCAT(reqby.firstname, ' ', reqby.lastname) as trby"),
                'special_external_customer.spcus_acctname',
                'special_external_customer.spcus_companyname'
            )
            ->groupBy(
                'special_external_gcrequest.spexgc_num',
                'special_external_gcrequest.spexgc_datereq',
                'approved_request.reqap_date',
                'reqby.firstname',
                'reqby.lastname',
                'special_external_customer.spcus_acctname',
                'special_external_customer.spcus_companyname'
            )

            ->orderBy('special_external_gcrequest.spexgc_datereq')
            ->get();

        $databar1 = DB::table('special_external_gcrequest_emp_assign')
            ->join('special_external_gcrequest', 'special_external_gcrequest.spexgc_id', '=', 'special_external_gcrequest_emp_assign.spexgcemp_trid')
            ->join('approved_request', 'approved_request.reqap_trid', '=', 'special_external_gcrequest.spexgc_id')
            ->where('approved_request.reqap_approvedtype', 'Special External GC Approved')
            ->where('special_external_gcrequest_emp_assign.spexgc_status', '!=', 'inactive')
            ->whereBetween(DB::raw("DATE_FORMAT(approved_request.reqap_date, '%Y-%m-%d')"), [$startDateData, $endDateData])
            ->select(
                'special_external_gcrequest_emp_assign.spexgcemp_denom',
                DB::raw("CONCAT(special_external_gcrequest_emp_assign.spexgcemp_fname,
                 ' ',special_external_gcrequest_emp_assign.spexgcemp_mname,
                 ' ',special_external_gcrequest_emp_assign.spexgcemp_lname ) AS customer_name"),
                'special_external_gcrequest_emp_assign.voucher',
                'special_external_gcrequest_emp_assign.spexgcemp_extname',
                'special_external_gcrequest_emp_assign.spexgcemp_barcode',
                'special_external_gcrequest.spexgc_num',
                DB::raw("DATE_FORMAT(special_external_gcrequest.spexgc_datereq, '%m/%d/%Y') as datereq"),
                DB::raw("DATE_FORMAT(approved_request.reqap_date, '%m/%d/%Y') as daterel")
            )
            ->orderBy('special_external_gcrequest_emp_assign.spexgcemp_barcode', 'asc')
            ->get();

        return Inertia::render('StoreAccounting/SPGC_Release', [
            'dataCustomer' => $datacus1,
            'dataBarcode' => $databar1,
            'fromDate' => $fromDate,
            'toDate' => $toDate
        ]);
    }
    public function DuplicatedBarcodes()
    {
        return Inertia::render('StoreAccounting/DuplicatedBarcode');
    }

    public function CheckVariance(Request $request)
    {
        $companyName = SpecialExternalCustomer::get();
        return Inertia::render('StoreAccounting/CheckVariance', [
            'customer' => $companyName
        ]);
    }
}
