<?php

namespace App\Http\Controllers;

use App\Helpers\ColumnHelper;
use App\Helpers\GetVerifiedGc;
use App\Http\Resources\PromoResource;
use App\Models\ApprovedGcrequest;
use App\Models\Gc;
use App\Models\GcRelease;
use App\Models\InstitutPayment;
use App\Models\InstitutTransaction;
use App\Models\InstitutTransactionsItem;
use App\Models\Promo;
use App\Models\Store;
use App\Models\StoreEodTextfileTransaction;

use App\Models\PromoGc;

use App\Models\SpecialExternalGcrequest;
use App\Models\SpecialExternalGcrequestItem;

use App\Models\Supplier;
use App\Models\StoreVerification;
use App\Models\TransactionSale;
use App\Models\TransactionStore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class MarketingController extends Controller
{

    public function index()
    {
        return Inertia::render(('Marketing/Dashboard'));
    }
    public function promoList(Request $request)
    {
        $tag = auth()->user()->promo_tag;
        $data = Promo::with('user:user_id,firstname,lastname,promo_tag')->filter($request->only('search'))
            ->where('promo.promo_tag', $tag)
            ->orderByDesc('promo.promo_id')
            ->paginate(10)->withQueryString(); 

        //Table Columns
        $columns = array_map(
            fn($name, $field) => ColumnHelper::arrayHelper($name, $field),
            ['Promo No', 'Promo Name', 'Date Notified', 'Expiration Date', 'Group', 'Created By', 'View'],
            ['promo_id', 'promo_name', 'promo_datenotified', 'promo_dateexpire', 'promo_group', 'fullname', 'View']
        );
        return Inertia::render('Marketing/PromoList', [
            'data' => PromoResource::collection($data),
            'columns' => ColumnHelper::getColumns($columns),
        ]);
    }



    public function addnewpromo()
    {
        return Inertia::render('Marketing/AddNewPromo');
    }

    public function promogcrequest()
    {
        return Inertia::render('Marketing/PromoGcRequest');
    }

    public function releasedpromogc()
    {
        return Inertia::render('Marketing/ReleasedPromoGc');
    }
    public function promoStatus(Request $request)
    {


        $tag = auth()->user()->promo_tag;

        $query = Gc::join('denomination', 'gc.denom_id', '=', 'denomination.denom_id')
            ->leftJoin('promo_gc_release_to_items', 'prreltoi_barcode', '=', 'barcode_no')
            ->leftJoin('promo_gc_release_to_details', 'prrelto_id', '=', 'prreltoi_relid')
            ->leftJoin('promo_gc_request', 'pgcreq_id', '=', 'prrelto_trid')
            ->leftJoin('promo_gc', 'prom_barcode', '=', 'barcode_no')
            ->leftJoin('promo', 'promo_id', '=', 'prom_promoid')
            ->leftJoin('promogc_released', 'prgcrel_barcode', '=', 'barcode_no')
            ->leftJoin('users', 'user_id', '=', 'prgcrel_by')
            ->where([['gc.gc_ispromo', '*'], ['gc.gc_validated', '*'], ['promo_gc_request.pgcreq_tagged', $tag]])
            ->whereAny(['barcode_no'], 'LIKE', '%' . $request->search . '%')
            ->select(
                'barcode_no',
                'promo_gc_request.pgcreq_group',
                'promo_gc_request.pgcreq_tagged',
                'denomination.denomination',
                'promo.promo_name',
                DB::raw("CONCAT(users.firstname, ' ', users.lastname) AS releasedby"),
                'promogc_released.prgcrel_claimant',
                'promogc_released.prgcrel_address',
                DB::raw("DATE_FORMAT(promogc_released.prgcrel_at, '%b %d %Y %h:%i %p') AS relat")
            )->orderByDesc('barcode_no')
            ->paginate(10)
            ->withQueryString();

        $query->transform(function ($item) {
            $item->status = is_null($item->promo_name) ? 'Available' : (!is_null($item->promo_name) && is_null($item->relat) ? 'Pending' : 'Released');
            return $item;
        });

        $columns = array_map(
            fn($name, $field) => ColumnHelper::arrayHelper($name, $field),
            ['GC Barcode #', 'Denomination', 'Retail Group', 'Promo Name', 'Customer Name', 'Customer Address', 'Status', 'Date Released', 'Released By'],
            ['barcode_no', 'denomination', 'pgcreq_group', 'promo_name', 'prgcrel_claimant', 'prgcrel_address', 'status', 'relat', 'releasedby'],
        );

        return Inertia::render('Marketing/PromoStatus', [
            'data' => $query,
            'columns' => ColumnHelper::getColumns($columns),
        ]);
    }

    public function manageSupplier()
    {
        $data = Supplier::paginate(10)->withQueryString();

        $columns = array_map(
            fn($name, $field) => ColumnHelper::arrayHelper($name, $field),
            ['Company Name', 'Account Name', 'Contact Person', 'Company Number', 'Address', 'View'],
            ['gcs_companyname', 'gcs_accountname', 'gcs_contactperson', 'gcs_contactnumber', 'gcs_address', 'View']
        );
        return Inertia::render('Marketing/ManageSupplier', [
            'data' => $data,
            'columns' => ColumnHelper::getColumns($columns),
        ]);
    }
    public function treasurySales(Request $request)
    {
        $search = $request->search;
        $data = InstitutPayment::select(
            'insp_id',
            'insp_trid',
            'insp_paymentcustomer',
            'institut_bankname',
            'institut_bankaccountnum',
            'institut_checknumber',
            'institut_amountrec',
            'insp_paymentnum',
            'institut_eodid'
        )
            ->whereAny(
                [
                    'insp_id',
                    'insp_trid',
                    'insp_paymentcustomer',
                    'institut_bankname',
                    'institut_bankaccountnum',
                    'institut_checknumber',
                    'institut_amountrec',
                    'insp_paymentnum',
                    'institut_eodid'
                ],
                'LIKE',
                '%' . $search . '%'
            )
            ->orderBy('insp_paymentnum', 'DESC')
            ->paginate(10)
            ->withQueryString();
        $data->transform(function ($item) {
            $customer = '';
            $datetr = '';
            $date = '';
            $time = '';
            $totgccnt = 0;
            $totdenom = 0;
            $paymenttype = '';

            switch ($item->insp_paymentcustomer) {
                case 'institution':
                    $transaction = DB::table('institut_transactions')
                        ->join('institut_customer', 'institut_customer.ins_id', '=', 'institut_transactions.institutr_cusid')
                        ->where('institutr_id', $item->insp_trid)
                        ->first();

                    if ($transaction) {
                        $paymenttype = $transaction->institutr_paymenttype;
                        $customer = $transaction->ins_name;
                        $datetr = $transaction->institutr_date;

                        $gcs = DB::table('institut_transactions_items')
                            ->join('gc', 'gc.barcode_no', '=', 'institut_transactions_items.instituttritems_barcode')
                            ->join('denomination', 'denomination.denom_id', '=', 'gc.denom_id')
                            ->select(DB::raw('IFNULL(COUNT(institut_transactions_items.instituttritems_barcode), 0) as cnt'), DB::raw('IFNULL(SUM(denomination.denomination), 0) as totamt'))
                            ->where('instituttritems_trid', $item->insp_trid)
                            ->first();

                        if ($gcs) {
                            $totgccnt = $gcs->cnt;
                            $totdenom = $gcs->totamt;
                        }
                    }
                    break;

                case 'stores':
                    $transaction = DB::table('approved_gcrequest')
                        ->join('store_gcrequest', 'store_gcrequest.sgc_id', '=', 'approved_gcrequest.agcr_request_id')
                        ->join('stores', 'stores.store_id', '=', 'store_gcrequest.sgc_store')
                        ->where('approved_gcrequest.agcr_id', $item->insp_trid)
                        ->first();

                    if ($transaction) {
                        $customer = $transaction->store_name;
                        $datetr = $transaction->agcr_approved_at;
                        $paymenttype = $transaction->agcr_paymenttype;

                        $gcs = DB::table('gc_release')
                            ->join('gc', 'gc.barcode_no', '=', 'gc_release.re_barcode_no')
                            ->join('denomination', 'denomination.denom_id', '=', 'gc.denom_id')
                            ->select(DB::raw('IFNULL(COUNT(gc_release.re_barcode_no), 0) as cnt'), DB::raw('IFNULL(SUM(denomination.denomination), 0) as totamt'))
                            ->where('rel_num', $transaction->agcr_request_relnum)
                            ->first();

                        if ($gcs) {
                            $totgccnt = $gcs->cnt;
                            $totdenom = $gcs->totamt;
                        }
                    }
                    break;

                case 'special external':
                    $transaction = DB::table('special_external_gcrequest')
                        ->join('special_external_customer', 'special_external_customer.spcus_id', '=', 'special_external_gcrequest.spexgc_company')
                        ->where('spexgc_id', $item->insp_trid)
                        ->first();

                    if ($transaction && $transaction->spexgc_addemp != 'pending') {
                        $customer = $transaction->spcus_companyname;
                        $datetr = $transaction->spexgc_datereq;
                        $paymenttype = $transaction->spexgc_paymentype == '1' ? 'cash' : 'check';

                        $gcs = DB::table('special_external_gcrequest_items')
                            ->select(DB::raw('IFNULL(SUM(specit_qty), 0) as cnt'), DB::raw('IFNULL(SUM(specit_denoms * specit_qty), 0) as totamt'))
                            ->where('specit_trid', $item->insp_trid)
                            ->first();

                        if ($gcs) {
                            $totgccnt = $gcs->cnt;
                            $totdenom = $gcs->totamt;
                        }
                    }
                    break;
            }


            if ($datetr) {
                $datetime = explode(' ', $datetr);
                $date = $datetime[0] ?? '';
                $time = $datetime[1] ?? '';
            }

            $item->date = $date;
            $item->time = $time;
            $item->customer = $customer;
            $item->totgccnt = $totgccnt;
            $item->totdenom = $totdenom;
            $item->paymenttype = $paymenttype;

            return $item;
        });



        $columns = array_map(
            fn($name, $field) => ColumnHelper::arrayHelper($name, $field),
            ['Transaction #', 'GC Type', 'Customer', 'Date', 'Time', 'GC pc(s)', 'Total Denom', 'Payment Type', 'View'],
            ['insp_id', 'insp_paymentcustomer', 'customer', 'date', 'time', 'totgccnt', 'totdenom', 'paymenttype', 'view']
        );

        return Inertia::render('Marketing/Sale_treasurySales', [
            'data' => $data,
            'columns' => ColumnHelper::getColumns($columns),
        ]);
    }


    public function storeSales()
    {
        $data = TransactionStore::join('stores', 'stores.store_id', '=', 'transaction_stores.trans_store')
            ->join('store_staff', 'store_staff.ss_id', '=', 'transaction_stores.trans_cashier')
            ->select(
                'trans_sid',
                'trans_number',
                'store_name',
                'trans_datetime',
                'trans_type'
            )
            ->whereIn('transaction_stores.trans_type', [1, 2, 3])
            ->orderByDesc('trans_number')
            ->paginate(10)
            ->withQueryString();

        $data->transform(function ($item) {

            $gcData = TransactionSale::join('gc', 'gc.barcode_no', '=', 'transaction_sales.sales_barcode')
                ->join('denomination', 'denomination.denom_id', '=', 'gc.denom_id')
                ->where('transaction_sales.sales_transaction_id', $item->trans_sid)
                ->selectRaw('IFNULL(COUNT(transaction_sales.sales_barcode), 0) as cnt')
                ->selectRaw('IFNULL(SUM(denomination.denomination), 0) as totamt')
                ->first();

            switch ($item->trans_type) {
                case '1':
                    $item->trans_type = 'Cash';
                    break;
                case '2':
                    $item->trans_type = 'Credit Card';
                    break;
                case '3':
                    $item->trans_type = 'AR';
                    break;
                default:
                    $item->trans_type = 'Unknown';
                    break;
            }

            $item->gcPc = $gcData->cnt;
            $item->totalDenom = $gcData->totamt;
            $transDatetime = $item->trans_datetime;
            $item->trans_date = date('Y-m-d', strtotime($transDatetime));
            $item->trans_time = date('H:i:s', strtotime($transDatetime));

            return $item;
        });

        $columns = array_map(
            fn($name, $field) => ColumnHelper::arrayHelper($name, $field),
            ['Transaction #', 'Store', 'Date', 'Time', 'GC pc(s)', 'Total Denom', 'Payment Type', 'View'],
            ['trans_number', 'store_name', 'trans_date', 'trans_time', 'gcPc', 'totalDenom', 'trans_type', 'View']
        );
        return Inertia::render('Marketing/Sale_storeSales', [
            'data' => $data,
            'columns' => ColumnHelper::getColumns($columns),
        ]);
    }


    public function verifiedGc_Amall(Request $request)
    {
        $search = $request->search;
        $data = GetVerifiedGc::getVerifiedGc(1, $search);
        return Inertia::render('Marketing/VerifiedGCperStore/AlturasMall', [
            'data' => $data,
            'columns' => ColumnHelper::$ver_gc_alturas_mall_columns,
        ]);
    }
    public function verifiedGc_A_talibon(Request $request)
    {
        $search = $request->search;
        $data = GetVerifiedGc::getVerifiedGc(2, $search);

        return Inertia::render('Marketing/VerifiedGCperStore/AlturasTalibon', [
            'data' => $data,
            'columns' => ColumnHelper::$ver_gc_alturas_mall_columns,
        ]);
    }
    public function verifiedGc_A_tubigon(Request $request)
    {
        $search = $request->search;
        $data = GetVerifiedGc::getVerifiedGc(0, $search);

        return Inertia::render('Marketing/VerifiedGCperStore/Alturastubigon', [
            'data' => $data,
            'columns' => ColumnHelper::$ver_gc_alturas_mall_columns,
        ]);
    }


    public function verifiedGc_AltaCita(Request $request)
    {
        $search = $request->search;
        $data = GetVerifiedGc::getVerifiedGc(8, $search);
        return Inertia::render('Marketing/VerifiedGCperStore/AltaCita', [
            'data' => $data,
            'columns' => ColumnHelper::$ver_gc_alturas_mall_columns,
        ]);
    }

    public function verifiedGc_AscTech(Request $request)
    {
        $search = $request->search;
        $data = GetVerifiedGc::getVerifiedGc(12, $search);
        return Inertia::render('Marketing/VerifiedGCperStore/AscTech', [
            'data' => $data,
            'columns' => ColumnHelper::$ver_gc_alturas_mall_columns,
        ]);
    }
    public function verifiedGc_colonadeColon(Request $request)
    {
        $search = $request->search;
        $data = GetVerifiedGc::getVerifiedGc(5, $search);
        return Inertia::render('Marketing/VerifiedGCperStore/Colonade_colon', [
            'data' => $data,
            'columns' => ColumnHelper::$ver_gc_alturas_mall_columns,
        ]);
    }
    public function verifiedGc_colonadeMandaue(Request $request)
    {
        $search = $request->search;
        $data = GetVerifiedGc::getVerifiedGc(6, $search);
        return Inertia::render('Marketing/VerifiedGCperStore/ColonadeMandaue', [
            'data' => $data,
            'columns' => ColumnHelper::$ver_gc_alturas_mall_columns,
        ]);
    }
    public function verifiedGc_plazaMarcela(Request $request)
    {
        $search = $request->search;
        $data = GetVerifiedGc::getVerifiedGc(4, $search);
        return Inertia::render('Marketing/VerifiedGCperStore/PlazaMarcela', [
            'data' => $data,
            'columns' => ColumnHelper::$ver_gc_alturas_mall_columns,
        ]);
    }
    public function verifiedGc_farmersMarket(Request $request)
    {
        $search = $request->search;
        $data = GetVerifiedGc::getVerifiedGc(10, $search);
        return Inertia::render('Marketing/VerifiedGCperStore/FarmersMarket', [
            'data' => $data,
            'columns' => ColumnHelper::$ver_gc_alturas_mall_columns,
        ]);
    }
    public function verifiedGc_udc(Request $request)
    {
        $search = $request->search;
        $data = GetVerifiedGc::getVerifiedGc(10, $search);
        return Inertia::render('Marketing/VerifiedGCperStore/UbayDistributionCenter', [
            'data' => $data,
            'columns' => ColumnHelper::$ver_gc_alturas_mall_columns,
        ]);
    }
    public function verifiedGc_screenville(Request $request)
    {
        $search = $request->search;
        $data = GetVerifiedGc::getVerifiedGc(11, $search);
        return Inertia::render('Marketing/VerifiedGCperStore/ScreenVille', [
            'data' => $data,
            'columns' => ColumnHelper::$ver_gc_alturas_mall_columns,
        ]);
    }
    public function verifiedGc_icm(Request $request)
    {
        $search = $request->search;
        $data = GetVerifiedGc::getVerifiedGc(3, $search);
        return Inertia::render('Marketing/VerifiedGCperStore/IslandCityMall', [
            'data' => $data,
            'columns' => ColumnHelper::$ver_gc_alturas_mall_columns,
        ]);
    }
    public function getPromoDetails(Request $request)
    {
        $data = PromoGc::join('denomination', 'denomination.denom_id', '=', 'promo_gc.prom_denom')
            ->join('gc_type', 'gc_type.gc_type_id', '=', 'promo_gc.prom_gctype')
            ->leftJoin('store_verification', 'store_verification.vs_barcode', '=', 'promo_gc.prom_barcode')
            ->select('promo_gc.prom_barcode', 'denomination.denomination', 'gc_type.gctype', 'store_verification.vs_barcode')
            ->where('promo_gc.prom_promoid', request()->id)
            ->where('promo_gc.prom_barcode', 'LIKE', '%' . request()->search . '%')
            ->get();
        $transformedData = $data->map(function ($item) {
            $item->gctype = ucfirst($item->gctype);
            return $item;
        });

        return response()->json(['data' => $transformedData]);
    }


    public function getBarcodeDetails()
    {
        $data = StoreVerification::join('customers', 'customers.cus_id', '=', 'store_verification.vs_cn')
            ->join('stores', 'stores.store_id', '=', 'store_verification.vs_store')
            ->join('users', 'users.user_id', '=', 'store_verification.vs_by')
            ->select(
                'store_verification.vs_date',
                'store_verification.vs_barcode',
                'store_verification.vs_time',
                'store_verification.vs_tf_denomination',
                'store_verification.vs_tf_balance',
                'store_verification.vs_tf_purchasecredit',
                'stores.store_name',
                'customers.cus_fname',
                'customers.cus_lname',
                'customers.cus_mname',
                'customers.cus_namext',
                'customers.cus_mobile',
                'customers.cus_address',
                'users.firstname',
                'users.lastname'
            )
            ->where('store_verification.vs_barcode', request()->id)
            ->get();


        return response()->json($data);
    }

    public function getStoreSaleDetails(Request $request)
    {
        $trid = $request->id;

        $dataTransactionStore = TransactionStore::join('stores', 'stores.store_id', '=', 'transaction_stores.trans_store')
            ->select('transaction_stores.trans_sid', 'transaction_stores.trans_number', 'stores.store_name')
            ->where('transaction_stores.trans_sid', $trid)->get();

        $dataTransactionSales = TransactionSale::join('denomination', 'denomination.denom_id', '=', 'transaction_sales.sales_denomination')
            ->leftJoin('store_verification', 'store_verification.vs_barcode', '=', 'transaction_sales.sales_barcode')
            ->leftJoin('stores', 'stores.store_id', '=', 'store_verification.vs_store')
            ->leftJoin('users', 'store_verification.vs_by', '=', 'users.user_id')
            ->leftJoin('customers', 'customers.cus_id', '=', 'store_verification.vs_cn')
            ->where('transaction_sales.sales_transaction_id', $trid)
            ->select([
                'transaction_sales.sales_barcode',
                'denomination.denomination',
                'stores.store_name',
                DB::raw("CONCAT(users.firstname, ' ', users.lastname) as verby"),
                DB::raw("CONCAT(customers.cus_fname, ' ', customers.cus_lname) as customer"),
                'store_verification.vs_date',
                'store_verification.vs_tf_used',
                'store_verification.vs_reverifydate',
                'store_verification.vs_reverifyby',
                'store_verification.vs_tf_balance'
            ])
            ->get();


        $columns = array_map(
            fn($name, $field) => ColumnHelper::arrayHelper($name, $field),
            ['Barcode #', 'Denomination', 'Store Verified', 'Verified By', 'Date Verified', 'Customer', 'Balance', 'View'],
            ['sales_barcode', 'denomination', 'store_name', 'verby', 'vs_date', 'customer', 'vs_tf_balance', 'View']
        );

        return response()->json([
            'dataTransStore' => $dataTransactionStore,
            'dataTransSales' => $dataTransactionSales,
            'selectedDataColumns' => $columns
        ]);
    }

    public function getTransactionPOSdetail(Request $request)
    {
        $data = StoreEodTextfileTransaction::where('seodtt_barcode', $request->id)
            ->select([
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
            ])
            ->get();
        $columns = array_map(
            fn($name, $field) => ColumnHelper::arrayHelper($name, $field),
            ['Textfile Line', 'Credit Limit', 'Cred. Pur. Amt + Add-on', 'Add-on Amt', 'Remaining Balance', 'Transaction #', 'Time of Cred Tranx', 'Bus. Unit', 'Terminal #', 'Ackslip #'],
            ['seodtt_line', 'seodtt_creditlimit', 'seodtt_credpuramt', 'seodtt_addonamt', 'seodtt_balance', 'seodtt_transno', 'seodtt_timetrnx', 'seodtt_bu', 'seodtt_terminalno', 'seodtt_ackslipno']
        );

        return response()->json([
            'title' => $request->id,
            'barcodeDetails' => $data,
            'selectedBarcodeColumns' => ColumnHelper::getColumns($columns)
        ]);
    }
}
