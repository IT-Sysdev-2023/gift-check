<?php

namespace App\Http\Controllers;

use App\Helpers\ColumnHelper;
use App\Helpers\GetVerifiedGc;
use App\Models\ApprovedGcrequest;
use App\Models\Gc;
use App\Models\GcRelease;
use App\Models\InstitutPayment;
use App\Models\InstitutTransaction;
use App\Models\InstitutTransactionsItem;
use App\Models\Promo;
<<<<<<< HEAD
use App\Models\PromoGc;
=======
use App\Models\SpecialExternalGcrequest;
use App\Models\SpecialExternalGcrequestItem;
>>>>>>> 0744ddf474722b54fa3e4e05ec3e89f2effe3429
use App\Models\Supplier;
use App\Models\StoreVerification;
use App\Models\TransactionSale;
use App\Models\TransactionStore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class MarketingController extends Controller
{

    public function promoList()
    {
        $tag = auth()->user()->promo_tag;
        $data = Promo::join('users', 'users.user_id', '=', 'promo.promo_valby')
            ->where('promo.promo_tag', $tag)
            ->select(
                'promo.promo_id',
                'promo.promo_name',
                'promo.promo_date',
                'promo.promo_datenotified',
                'promo.promo_dateexpire',
                'promo.promo_remarks',
                'promo.promo_drawdate',
                DB::raw("CONCAT(UCASE(SUBSTRING(users.firstname, 1, 1)), SUBSTRING(users.firstname, 2), ' ', UCASE(SUBSTRING(users.lastname, 1, 1)), SUBSTRING(users.lastname, 2)) AS fullname"),
                'users.promo_tag',
                'promo.promo_group'
            )
            ->orderByDesc('promo.promo_id')
            ->paginate(10)->withQueryString();

        $columns = array_map(
            fn ($name, $field) => ColumnHelper::arrayHelper($name, $field),
            ['Promo No', 'Promo Name', 'Date Notified', 'Expiration Date', 'Group', 'Created By', 'View'],
            ['promo_id', 'promo_name', 'promo_datenotified', 'promo_dateexpire', 'promo_group', 'fullname', 'View']
        );
        return Inertia::render('Marketing/PromoList', [
            'data' => $data,
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
    public function promoStatus()
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
            ->where('gc.gc_ispromo', '*')
            ->where('gc.gc_validated', '*')
            ->where('promo_gc_request.pgcreq_tagged', $tag)
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
            fn ($name, $field) => ColumnHelper::arrayHelper($name, $field),
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
            fn ($name, $field) => ColumnHelper::arrayHelper($name, $field),
            ['Company Name', 'Account Name', 'Contact Person', 'Company Number', 'Address', 'View'],
            ['gcs_companyname', 'gcs_accountname', 'gcs_contactperson', 'gcs_contactnumber', 'gcs_address', 'View']
        );
        return Inertia::render('Marketing/ManageSupplier', [
            'data' => $data,
            'columns' => ColumnHelper::getColumns($columns),
        ]);
    }
    public function treasurySales()
    {
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
<<<<<<< HEAD
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
            fn ($name, $field) => ColumnHelper::arrayHelper($name, $field),
            ['Transaction #', 'GC Type', 'Customer', 'Date', 'Time', 'GC pc(s)', 'Total Denom', 'Payment Type', 'View'],
            ['insp_id', 'insp_paymentcustomer', 'customer', 'date', 'time', 'totgccnt', 'totdenom', 'paymenttype', 'view']
        );

        return Inertia::render('Marketing/Sale_treasurySales', [
=======
            ->orderByDesc('insp_paymentnum')
            ->limit(10)
            ->get();

        $data->transform(function ($p) {
            $datetr = $totgccnt = $totdenom = $customer = $paymenttype = '';

            if ($p->insp_paymentcustomer == 'institution') {
                $query = InstitutTransaction::join('institut_customer', 'institut_customer.ins_id', '=', 'institut_transactions.institutr_cusid')
                    ->where('institut_transactions.institutr_id', $p->insp_trid)
                    ->select('institut_transactions.institutr_id', 'institut_transactions.institutr_trnum', 'institut_transactions.institutr_paymenttype', 'institut_transactions.institutr_date', 'institut_customer.ins_name')
                    ->first();

                if ($query) {
                    $paymenttype = $query->institutr_paymenttype;
                    $customer = $query->ins_name;
                    $datetr = $query->institutr_date;

                    $query_gcs = InstitutTransactionsItem::join('gc', 'gc.barcode_no', '=', 'institut_transactions_items.instituttritems_barcode')
                        ->join('denomination', 'denomination.denom_id', '=', 'gc.denom_id')
                        ->where('instituttritems_trid', $p->insp_trid)
                        ->select(DB::raw('IFNULL(COUNT(institut_transactions_items.instituttritems_barcode),0) as cnt'), DB::raw('IFNULL(SUM(denomination.denomination),0) as totamt'))
                        ->first();

                    if ($query_gcs) {
                        $totgccnt = $query_gcs->cnt;
                        $totdenom = $query_gcs->totamt;
                    }
                }
            } elseif ($p->insp_paymentcustomer == 'stores') {
                $query = ApprovedGcrequest::join('store_gcrequest', 'store_gcrequest.sgc_id', '=', 'approved_gcrequest.agcr_request_id')
                    ->join('stores', 'stores.store_id', '=', 'store_gcrequest.sgc_store')
                    ->where('approved_gcrequest.agcr_id', $p->insp_trid)
                    ->select('approved_gcrequest.agcr_request_id', 'approved_gcrequest.agcr_request_relnum', 'approved_gcrequest.agcr_approved_at', 'approved_gcrequest.agcr_paymenttype', 'stores.store_name')
                    ->first();

                if ($query) {
                    $customer = $query->store_name;
                    $datetr = $query->agcr_approved_at;
                    $paymenttype = $query->agcr_paymenttype;

                    $query_gcs = GcRelease::join('gc', 'gc.barcode_no', '=', 'gc_release.re_barcode_no')
                        ->join('denomination', 'denomination.denom_id', '=', 'gc.denom_id')
                        ->where('rel_num', $query->agcr_request_relnum)
                        ->select(DB::raw('IFNULL(COUNT(gc_release.re_barcode_no),0) as cnt'), DB::raw('IFNULL(SUM(denomination.denomination),0) as totamt'))
                        ->first();

                    if ($query_gcs) {
                        $totgccnt = $query_gcs->cnt;
                        $totdenom = $query_gcs->totamt;
                    }
                }
            } elseif ($p->insp_paymentcustomer == 'special external') {
                $query = SpecialExternalGcrequest::join('special_external_customer', 'special_external_customer.spcus_id', '=', 'special_external_gcrequest.spexgc_company')
                    ->where('special_external_gcrequest.spexgc_id', $p->insp_trid)
                    ->select('special_external_gcrequest.spexgc_id', 'special_external_gcrequest.spexgc_datereq', 'special_external_customer.spcus_companyname', 'special_external_gcrequest.spexgc_paymentype', 'special_external_gcrequest.spexgc_addemp')
                    ->first();

                if ($query) {
                    if ($query->spexgc_addemp == 'pending') {
                        return null; // Skip this record
                    }

                    $customer = $query->spcus_companyname;
                    $datetr = $query->spexgc_datereq;
                    $paymenttype = $query->spexgc_paymentype == '1' ? 'cash' : 'check';

                    $query_gcs = SpecialExternalGcrequestItem::where('specit_trid', $p->insp_trid)
                        ->select(DB::raw('IFNULL(SUM(special_external_gcrequest_items.specit_qty),0) as cnt'), DB::raw('IFNULL(SUM(special_external_gcrequest_items.specit_denoms * special_external_gcrequest_items.specit_qty),0) as totamt'))
                        ->first();

                    if ($query_gcs) {
                        $totgccnt = $query_gcs->cnt;
                        $totdenom = $query_gcs->totamt;
                    }
                }
            }

            $p->customer = $customer;
            $p->datetr = $datetr;
            $p->totgccnt = $totgccnt;
            $p->totdenom = $totdenom;
            $p->paymenttype = $paymenttype;

            return $p;
        });

        dd($data->toArray());
        $columns = array_map(
            fn ($name, $field) => ColumnHelper::arrayHelper($name, $field),
            ['Transaction #', 'GC Type', 'Customer', 'Date', 'Time', 'GC pc(s)','Total Denom','Payment Type','View'],
            ['insp_id', 'gcs_accountname', 'gcs_contactperson', 'gcs_contactnumber', 'gcs_address', 'View']
        );

        
        return Inertia::render('Marketing/Sale_treasurySales',[
>>>>>>> 0744ddf474722b54fa3e4e05ec3e89f2effe3429
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
            fn ($name, $field) => ColumnHelper::arrayHelper($name, $field),
            ['Transaction #', 'Store', 'Date', 'Time', 'GC pc(s)', 'Total Denom', 'Payment Type', 'View'],
            ['trans_number', 'store_name', 'trans_date', 'trans_time', 'gcPc', 'totalDenom', 'trans_type', 'view']
        );
        return Inertia::render('Marketing/Sale_storeSales', [
            'data' => $data,
            'columns' => ColumnHelper::getColumns($columns),
        ]);
    }


    public function verifiedGc_Amall()
    {

        $data = GetVerifiedGc::getVerifiedGc(1);
        return Inertia::render('Marketing/VerifiedGCperStore/AlturasMall', [
            'data' => $data,
            'columns' => ColumnHelper::$ver_gc_alturas_mall_columns,
        ]);
    }
    public function verifiedGc_A_talibon()
    {
        $data = GetVerifiedGc::getVerifiedGc(2);

        return Inertia::render('Marketing/VerifiedGCperStore/AlturasTalibon', [
            'data' => $data,
            'columns' => ColumnHelper::$ver_gc_alturas_mall_columns,
        ]);
    }
    public function verifiedGc_A_tubigon()
    {

        $data = GetVerifiedGc::getVerifiedGc(0);

        return Inertia::render('Marketing/VerifiedGCperStore/Alturastubigon', [
            'data' => $data,
            'columns' => ColumnHelper::$ver_gc_alturas_mall_columns,
        ]);
    }


    public function verifiedGc_AltaCita()
    {
        $data = GetVerifiedGc::getVerifiedGc(8);
        return Inertia::render('Marketing/VerifiedGCperStore/AltaCita', [
            'data' => $data,
            'columns' => ColumnHelper::$ver_gc_alturas_mall_columns,
        ]);
    }

    public function verifiedGc_AscTech()
    {
        $data = GetVerifiedGc::getVerifiedGc(12);
        return Inertia::render('Marketing/VerifiedGCperStore/AscTech', [
            'data' => $data,
            'columns' => ColumnHelper::$ver_gc_alturas_mall_columns,
        ]);
    }
    public function verifiedGc_colonadeColon()
    {
        $data = GetVerifiedGc::getVerifiedGc(5);
        return Inertia::render('Marketing/VerifiedGCperStore/Colonade_colon', [
            'data' => $data,
            'columns' => ColumnHelper::$ver_gc_alturas_mall_columns,
        ]);
    }
    public function verifiedGc_colonadeMandaue()
    {
        $data = GetVerifiedGc::getVerifiedGc(6);
        return Inertia::render('Marketing/VerifiedGCperStore/ColonadeMandaue', [
            'data' => $data,
            'columns' => ColumnHelper::$ver_gc_alturas_mall_columns,
        ]);
    }
    public function verifiedGc_plazaMarcela()
    {
        $data = GetVerifiedGc::getVerifiedGc(4);
        return Inertia::render('Marketing/VerifiedGCperStore/PlazaMarcela', [
            'data' => $data,
            'columns' => ColumnHelper::$ver_gc_alturas_mall_columns,
        ]);
    }
    public function verifiedGc_farmersMarket()
    {
        $data = GetVerifiedGc::getVerifiedGc(10);
        return Inertia::render('Marketing/VerifiedGCperStore/FarmersMarket', [
            'data' => $data,
            'columns' => ColumnHelper::$ver_gc_alturas_mall_columns,
        ]);
    }
    public function verifiedGc_udc()
    {
        $data = GetVerifiedGc::getVerifiedGc(10);
        return Inertia::render('Marketing/VerifiedGCperStore/UbayDistributionCenter', [
            'data' => $data,
            'columns' => ColumnHelper::$ver_gc_alturas_mall_columns,
        ]);
    }
    public function verifiedGc_screenville()
    {
        $data = GetVerifiedGc::getVerifiedGc(11);
        return Inertia::render('Marketing/VerifiedGCperStore/ScreenVille', [
            'data' => $data,
            'columns' => ColumnHelper::$ver_gc_alturas_mall_columns,
        ]);
    }
    public function verifiedGc_icm()
    {
        $data = GetVerifiedGc::getVerifiedGc(3);
        return Inertia::render('Marketing/VerifiedGCperStore/IslandCityMall', [
            'data' => $data,
            'columns' => ColumnHelper::$ver_gc_alturas_mall_columns,
        ]);
    }
    public function getPromoDetails()
    {
        $data = PromoGc::join('denomination', 'denomination.denom_id', '=', 'promo_gc.prom_denom')
            ->join('gc_type', 'gc_type.gc_type_id', '=', 'promo_gc.prom_gctype')
            ->leftJoin('store_verification', 'store_verification.vs_barcode', '=', 'promo_gc.prom_barcode')
            ->select('promo_gc.prom_barcode', 'denomination.denomination', 'gc_type.gctype', 'store_verification.vs_barcode')
            ->where('promo_gc.prom_promoid', request()->id)
            ->where('promo_gc.prom_barcode', 'LIKE', '%' . request()->search .'%')
            ->get();

        $transformedData = $data->map(function ($item) {
            $item->gctype = ucfirst($item->gctype);
            return $item;
        });

        return response()->json($transformedData);
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
}
