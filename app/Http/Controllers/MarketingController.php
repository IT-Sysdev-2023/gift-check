<?php

namespace App\Http\Controllers;

use App\Helpers\ColumnHelper;
use App\Helpers\GetVerifiedGc;
use App\Http\Resources\PromoResource;
use App\Models\ApprovedProductionRequest;
use App\Models\ApprovedRequest;
use App\Models\Assignatory;
use App\Models\CancelledProductionRequest;
use App\Models\Gc;
use App\Models\InstitutPayment;
use App\Models\Promo;
use App\Models\SpecialExternalGcrequest;
use App\Models\StoreEodTextfileTransaction;
use App\Models\User;
use App\Models\Denomination;
use App\Models\Document;
use App\Models\InstitutTransactionsItem;
use App\Models\LedgerBudget;
use App\Models\LedgerCheck;
use App\Models\ProductionRequest;
use App\Models\ProductionRequestItem;
use App\Models\TempPromo;
use App\Models\PromoGc;
use App\Models\PromogcReleased;
use App\Models\PromoGcReleaseToItem;
use App\Models\PromoGcRequest;
use App\Models\PromoGcRequestItem;
use App\Models\RequisitionEntry;
use App\Models\SpecialExternalCustomer;
use App\Models\SpecialExternalGcrequestEmpAssign;
use App\Models\SpecialExternalGcrequestItem;
use App\Models\Supplier;
use App\Models\StoreVerification;
use App\Models\TransactionSale;
use App\Models\TransactionStore;
use App\Models\UserDetails;
use App\Services\Marketing\PdfServices;
use App\Services\Marketing\MarketingServices;
use App\Services\Treasury\RegularGcProcessService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\View\View;
use PHPUnit\Event\Test\Prepared;

use function Pest\Laravel\json;

class MarketingController extends Controller
{
    public function __construct(
        public MarketingServices $marketing,
        public RegularGcProcessService $RegularGc
    ) {

    }
    public static function productionRequest($id)
    {
        $productionReqItems = ProductionRequestItem::join(
            'denomination',
            'production_request_items.pe_items_denomination',
            '=',
            'denomination.denom_id'
        )->selectFilter()
            ->where('pe_items_request_id', $id)->get();

        foreach ($productionReqItems as $key => $value) {
            $data = Gc::select('barcode_no')
                ->where('denom_id', $value->pe_items_denomination ?? null)
                ->where('pe_entry_gc', $id)
                ->orderBy('barcode_no')
                ->get();

            $barStart = $data->first()->barcode_no ?? null;
            $barEnd = $data->last()->barcode_no ?? null;
            $value->barcodeStart = $barStart;
            $value->barcodeEnd = $barEnd;

        }
        $productionReqItems->transform(function ($item) {
            $item->total = $item->denomination * $item->pe_items_quantity;

            return $item;
        });
        return $productionReqItems;

    }

    public function index(Request $request)
    {
        $specialGc = $this->marketing->countspecialgc();
        $promoGcRequest = $this->marketing->promoGcRequest();
        $gcProductionRequest = $this->marketing->productionRequest();
        $supplier = Supplier::where('gcs_status', 1)->get();
        $checkedBy = $this->marketing->checkedBy();
        $currentBudget = $this->marketing->currentBudget();
        $requestNum = ProductionRequest::where('pe_generate_code', 1)
            ->where('pe_requisition', 0)
            ->where('pe_status', 1)->get();
        $productionReqItems = self::productionRequest($request->data);
        $columns = array_map(
            fn($name, $field) => ColumnHelper::arrayHelper($name, $field),
            ['Denomination', 'Qty', 'Barcode No. Start', 'Barcode No. End'],
            ['denomination', 'pe_items_quantity', 'barcodeStart', 'barcodeEnd']
        );

        $query = RequisitionEntry::orderByDesc('requis_erno')->first();
        $getRequestNo = intval($query->requis_erno ?? 0) + 1;
        $getRequestNo = sprintf('%04d', $getRequestNo);



        return Inertia::render(('Marketing/MarketingDashboard'), [
            'getRequestNo' => $getRequestNo,
            'ReqNum' => $requestNum,
            'currentBudget' => $currentBudget,
            'checkBy' => $checkedBy,
            'supplier' => $supplier,
            'productionReqItems' => $productionReqItems,
            'columns' => ColumnHelper::getColumns($columns),
            'gcProductionRequest' => $gcProductionRequest,
            'countPromoGcRequest' => $promoGcRequest,
            'specialgcount' => $specialGc
        ]);
    }
    public function promoList(Request $request)
    {
        $tag = $request->user()->promo_tag;
        $data = Promo::with('user:user_id,firstname,lastname,promo_tag')->filter($request->only('search'))
            ->where('promo.promo_tag', $tag)
            ->orderByDesc('promo.promo_id')
            ->paginate(10)->withQueryString();

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

        $promoNum = promo::count() + 1;

        return Inertia::render('Marketing/AddNewPromo', [
            'PromoNum' => $promoNum,
            'promoId' => $promoNum
        ]);
    }

    public function promogcrequest(Request $request)
    {
        $tag = $request->user()->promo_tag;

        $denom = Denomination::where('denom_type', 'RSGC')
            ->where('denom_status', 'active')->get();
        $denomCol = ColumnHelper::denomCols();

        $pgcreq_reqnum = PromoGcRequest::select('pgcreq_reqnum')
            ->where('pgcreq_tagged', '=', $tag)
            ->orderByDesc('pgcreq_reqnum')
            ->first();
        $num = $pgcreq_reqnum ? (int) $pgcreq_reqnum->pgcreq_reqnum : 0;
        $formatted_num = str_pad($num + 1, 3, '0', STR_PAD_LEFT);
        $rfprom_number = $formatted_num;


        return Inertia::render('Marketing/PromoGcRequest', [
            'rfprom_number' => $rfprom_number,
            'denomination' => $denom,
            'denomCol' => $denomCol
        ]);
    }

    public function releasedpromogc()
    {
        return Inertia::render('Marketing/ReleasedPromoGc');
    }
    public function promoStatus(Request $request)
    {


        $tag = $request->user()->promo_tag;

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
            $item->status = is_null($item->promo_name)
                ? 'Available' : (!is_null($item->promo_name)
                    && is_null($item->relat) ? 'Pending' : 'Released');
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
            ['Company Name', 'Account Name', 'Contact Person', 'Company Number', 'Address', 'Action'],
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
            ['insp_id', 'insp_paymentcustomer', 'customer', 'date', 'time', 'totgccnt', 'totdenom', 'paymenttype', 'View']
        );

        return Inertia::render('Marketing/Sale_treasurySales', [
            'data' => $data,
            'columns' => ColumnHelper::getColumns($columns),
        ]);
    }

    public function viewTreasurySales(Request $request)
    {
        $query = InstitutTransactionsItem::select([
            'institut_transactions_items.instituttritems_barcode',
            'denomination.denomination',
            'stores.store_name',
            'store_verification.vs_date',
            DB::raw("CONCAT(users.firstname, ' ', users.lastname) as verby"),
            DB::raw("CONCAT(customers.cus_fname, ' ', customers.cus_lname) as customer"),
            'store_verification.vs_tf_used',
            'store_verification.vs_reverifydate',
            'store_verification.vs_reverifyby',
            'store_verification.vs_tf_balance'
        ])
            ->join('gc', 'gc.barcode_no', '=', 'institut_transactions_items.instituttritems_barcode')
            ->join('denomination', 'denomination.denom_id', '=', 'gc.denom_id')
            ->leftJoin('store_verification', 'store_verification.vs_barcode', '=', 'institut_transactions_items.instituttritems_barcode')
            ->leftJoin('stores', 'stores.store_id', '=', 'store_verification.vs_store')
            ->leftJoin('users', 'store_verification.vs_by', '=', 'users.user_id')
            ->leftJoin('customers', 'customers.cus_id', '=', 'store_verification.vs_cn')
            ->where('instituttritems_trid', '=', $request->id)
            ->get();

        $query->transform(function ($item) use ($request) {
            $item->gcType = $request->data['insp_paymentcustomer'];
            return $item;
        });
        return response()->json([
            'data' => $query
        ]);
    }


    public function storeSales(Request $request)
    {
        $search = $request->search;
        
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
            ->whereAny(['trans_number',], 'LIKE', '%' . $search . '%')
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
    public function getPromoDetails()
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
            'selectedDataColumns' => ColumnHelper::getColumns($columns)
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


    public function submitPromoGcRequest(Request $request)
    {
        $request->validate([
            'file' => 'required',
            'remarks' => 'required',
            'quantities' => 'required',
            'totalValue' => 'required',
        ]);

        $totalrequest = str_replace(',', '', $request->totalValue);

        $num = PromoGcRequest::select('pgcreq_reqnum')->where('pgcreq_tagged', $request->user()->promo_tag)->orderByDesc('pgcreq_reqnum')->first();

        $relnum = is_null($num) ? 1 : $num->pgcreq_reqnum + 1;

        $reqNum = sprintf("%03d", $relnum);

        $denomination = collect($request['quantities'])->filter(function ($item) {
            return $item !== null;
        });

        if ($request->user()->user_id == '8') {
            $group = $request->user()->usergroup;
        } else {
            $group = $request['group'];
        }

        DB::transaction(function () use ($request, $reqNum, $group, $denomination, $totalrequest) {
            $promo = PromoGcRequest::create([
                'pgcreq_reqnum' => $reqNum,
                'pgcreq_reqby' => $request->user()->user_id,
                'pgcreq_datereq' => now(),
                'pgcreq_dateneeded' => Date::parse($request['dateneeded']['$d'])->format('Y-m-d'),
                'pgcreq_status' => 'pending',
                'pgcreq_remarks' => $request['remarks'],
                'pgcreq_total' => (float) $totalrequest,
                'pgcreq_group' => $group,
                'pgcreq_tagged' => $request->user()->promo_tag,
                'pgcreq_group_status' => '',
                'pgcreq_doc' => $this->marketing->fileUpload($request, $reqNum)
            ]);

            $trid = is_null($promo->pgcreq_id) ? '1' : $promo->pgcreq_id;

            foreach ($denomination as $key => $value) {
                PromoGcRequestItem::create([
                    'pgcreqi_trid' => $trid,
                    'pgcreqi_denom' => $key,
                    'pgcreqi_qty' => $value,
                    'pgcreqi_remaining' => $value
                ]);
            }

        });
        return back()->with([
            'msg' => "Success",
            'description' => "Request Saved",
            'type' => "success",
        ]);


    }

    public function validateGc(Request $request)
    {
        $barcode = $request->barcode;
        $group = $request->group;
        $tag = $request->user()->promo_tag;
        $promoid = $request->promoNo;
        $gctype = 1;
        $response = ['stat' => 0];

        if (!Gc::where('barcode_no', $barcode)->exists()) {
            $response['msg'] = 'GC Barcode #' . $barcode . ' not found.';
        } elseif (PromoGc::where('prom_barcode', $barcode)->exists()) {
            $response['msg'] = 'GC Barcode #' . $barcode . ' already validated for promo.';
        } elseif (TempPromo::where('tp_barcode', $barcode)->exists()) {
            $response['msg'] = 'GC Barcode #' . $barcode . ' already scanned for promo validation.';
        } else {
            $denom = Gc::where('barcode_no', $barcode)->value('denom_id');

            $promo = PromoGcReleaseToItem::select(
                'promo_gc_release_to_items.prreltoi_barcode',
                'gc.gc_validated',
                'gc.gc_ispromo',
                'gc.denom_id',
                'promo_gc_request.pgcreq_group',
                'promo_gc_request.pgcreq_tagged'
            )
                ->leftJoin('gc', 'gc.barcode_no', '=', 'promo_gc_release_to_items.prreltoi_barcode')
                ->leftJoin('promo_gc_release_to_details', 'promo_gc_release_to_details.prrelto_id', '=', 'promo_gc_release_to_items.prreltoi_relid')
                ->leftJoin('promo_gc_request', 'promo_gc_request.pgcreq_id', '=', 'promo_gc_release_to_details.prrelto_trid')
                ->where('promo_gc_release_to_items.prreltoi_barcode', $barcode)
                ->first();

            if (!$promo) {
                $response['msg'] = 'GC Barcode #' . $barcode . ' not found.';
            } elseif (empty($promo->gc_validated) || empty($promo->gc_ispromo)) {
                $response['msg'] = 'GC Barcode #' . $barcode . ' is not for Promo.';
            } elseif ($promo->pgcreq_group != $group) {
                $response['msg'] = 'GC Barcode #' . $barcode . ' does not belong to Group ' . $group . '.';
            } elseif ($promo->pgcreq_tagged != $tag) {
                $response['msg'] = 'GC Barcode #' . $barcode . ' not found.';
            } else {




                $inserted = TempPromo::insert([
                    'tp_barcode' => $barcode,
                    'tp_den' => $denom,
                    'tp_promoid' => $promoid,
                    'tp_by' => $request->user()->id,
                    'tp_gctype' => $gctype,
                ]);

                if ($inserted) {
                    $response['stat'] = 1;
                    $response['msg'] = 'GC Barcode #' . $barcode . ' successfully validated for Group ' . $group . ' promo.';
                    $response['den'] = $denom;
                } else {
                    $response['msg'] = 'Failed to insert data for GC Barcode #' . $barcode . '.';
                }
            }
        }



        return response()->json($response);
    }

    public function getdenom()
    {
        $data = Denomination::with('getDenom')
            ->where('denom_type', 'RSGC')
            ->where('denom_status', 'active')
            ->get()
            ->map(function ($denomination) {
                return [
                    "denom_id" => $denomination->denom_id,
                    "denom_code" => $denomination->denom_code,
                    "denomination" => $denomination->denomination,
                    "countDen" => $denomination->getDenom->count(),

                ];
            });
        $columns = array_map(
            fn($name, $field) => ColumnHelper::arrayHelper($name, $field),
            ['Denomination', 'Scanned GC'],
            ['denomination', 'denom_id']
        );

        return response()->json([
            'data' => $data,
            'columns' => ColumnHelper::getColumns($columns),
        ]);
    }

    public function gcpromovalidation(Request $request)
    {


        $columns = array_map(
            fn($name, $field) => ColumnHelper::arrayHelper($name, $field),
            ['Denomination', 'Scanned GC'],
            ['denomination', 'denom_id']
        );

        $denom = Gc::select('denom_id')->where('barcode_no', $request->barcode)->get();
        $response = [];
        $barcode = $request->barcode;


        if (!Gc::where('barcode_no', $request->barcode)->exists()) {
            $response = ['msg' => 'Opps! Error', 'description' => 'Barcode does not exist', 'type' => 'error'];
        } elseif (PromoGc::where('prom_barcode', $request->barcode)->exists()) {
            $response = ['msg' => 'Opps! Error', 'description' => $request->barcode . ' is already validated for promo', 'type' => 'error'];
        } elseif (TempPromo::where('tp_barcode', $request->barcode)->exists()) {
            $response = ['msg' => 'Opps! Warning', 'description' => $request->barcode . ' already scanned for promo validation', 'type' => 'warning'];
        } else {




            $promo = PromoGcReleaseToItem::select([
                'promo_gc_release_to_items.prreltoi_barcode',
                'gc.gc_validated',
                'gc.gc_ispromo',
                'gc.denom_id',
                'promo_gc_request.pgcreq_group',
                'promo_gc_request.pgcreq_tagged'
            ])
                ->where('promo_gc_release_to_items.prreltoi_barcode', $barcode)
                ->join('gc', 'gc.barcode_no', '=', 'promo_gc_release_to_items.prreltoi_barcode')
                ->leftJoin('promo_gc_release_to_details', 'promo_gc_release_to_details.prrelto_id', '=', 'promo_gc_release_to_items.prreltoi_relid')
                ->leftJoin('promo_gc_request', 'promo_gc_request.pgcreq_id', '=', 'promo_gc_release_to_details.prrelto_trid')
                ->get();

            if (!count($promo) > 0) {
                $response = ['msg' => 'Opps! Error', 'description' => 'Barcode does not exist', 'type' => 'error'];
            } elseif ($promo[0]->gc_validated == '' || $promo[0]->gc_ispromo == '') {
                $response = ['msg' => 'Opps! Error', 'description' => 'Barcode ' . $barcode . ' is not for Promo', 'type' => 'error'];
            } elseif ($promo[0]->pgcreq_group != $request->promoGroup) {
                $response = ['msg' => 'Opps! Error', 'description' => 'Barcode ' . $barcode . ' does not belong to Group ' . $request->promoGroup, 'type' => 'error'];
            } else {
                $tempData = TempPromo::insert([
                    'tp_barcode' => $barcode,
                    'tp_den' => $denom[0]->denom_id,
                    'tp_promoid' => $request->promoId,
                    'tp_by' => $request->user()->user_id,
                    'tp_gctype' => $request->gctype,
                ]);

                if ($tempData) {
                    $response = [
                        'msg' => 'Success',
                        'description' => 'Barcode ' . $barcode . ' successfully validated for Group: ' . $request->promoGroup,
                        'type' => 'success'
                    ];
                    // $data = $getDenomination;
                } else {
                    $response = ['msg' => 'Opps! Error', 'description' => 'Failed to insert data for Barcode: ' . $barcode, 'type' => 'error'];
                }
            }
        }
        return response()->json([
            'data' => self::getDenomination(),
            'columns' => ColumnHelper::getColumns($columns),
            'response' => $response
        ]);
    }

    public function truncate()
    {
        TempPromo::truncate();
    }


    public function scannedGc()
    {
        $scannedGcdata = TempPromo::join('denomination', 'denom_id', '=', 'tp_den')->get();
        $scannedCol = array_map(
            fn($name, $field) => ColumnHelper::arrayHelper($name, $field),
            ['Barcode', 'Denomination', 'GC Type', 'Action'],
            ['tp_barcode', 'denomination', 'tp_gctype', 'action']
        );

        $columns = array_map(
            fn($name, $field) => ColumnHelper::arrayHelper($name, $field),
            ['Denomination', 'Scanned GC'],
            ['denomination', 'denom_id']
        );

        return response()->json([
            'scannedGcdata' => $scannedGcdata,
            'scannedCol' => $scannedCol,
            'columns' => ColumnHelper::getColumns($columns)
        ]);
    }

    public static function getDenomination()
    {
        return Denomination::with('getDenom')
            ->where('denom_type', 'RSGC')
            ->where('denom_status', 'active')
            ->get()
            ->map(function ($denomination) {
                return [
                    "denom_id" => $denomination->denom_id,
                    "denom_code" => $denomination->denom_code,
                    "denomination" => $denomination->denomination,
                    "countDen" => $denomination->getDenom->count(),

                ];
            });
    }

    public function removeGc(Request $request)
    {
        $barcode = $request->barcode;
        $isDeleted = TempPromo::where('tp_barcode', $barcode)->delete();

        $response = [
            'msg' => $isDeleted ? 'Removed' : 'Oops! Error',
            'description' => $isDeleted
                ? "Barcode $barcode has been removed"
                : 'Something went wrong',
            'type' => $isDeleted ? 'success' : 'error'
        ];

        return response()->json([
            'response' => $response,
            'dataScanned' => TempPromo::join('denomination', 'denom_id', '=', 'tp_den')->get(),
            'data' => self::getDenomination(),
        ]);
    }


    public function newpromo(Request $request)
    {

        $tempbarcodes = TempPromo::where('tp_by', $request->user()->user_id)->get();


        $data = $request->data;

        $response = [];
        // $data = ($request->data);
        $tag = $request->user()->promo_tag;

        $notes = $data['details'];
        $promoName = $data['promoName'];
        $promoGroup = $data['promoGroup'];
        $drawDate = $data['drawDate'];
        $dateNotify = $data['dateNotify'];


        if (empty($notes) || empty($promoName) || empty($drawDate) || empty($promoGroup) || empty($dateNotify)) {
            $response = ['msg' => 'Opps! error', 'description' => 'Please fill all the required fields', 'type' => 'error'];
        } else if (empty($tempbarcodes)) {
            $response = ['msg' => 'Opps! error', 'description' => 'Please scan barcodes', 'type' => 'error'];
        } else {
            if ($tempbarcodes) {

                DB::transaction(function () use ($tempbarcodes, $data, $promoName, $promoGroup, $tag, $notes) {

                    Promo::create([
                        'promo_id' => $data['promoNo'],
                        'promo_num' => $data['promoNo'],
                        'promo_name' => $promoName,
                        'promo_group' => $promoGroup,
                        'promo_tag' => $tag,
                        'promo_date' => Date::parse($data['dateCreated'])->format('Y-m-d'),
                        'promo_remarks' => $notes,
                        'promo_valby' => $data['prepby'],
                        'promo_dateexpire' => Date::parse($data['expiryDate'])->format('Y-m-d'),
                        'promo_datenotified' => Date::parse($data['dateNotify'])->format('Y-m-d'),
                        'promo_drawdate' => Date::parse($data['drawDate'])->format('Y-m-d')
                    ]);


                    foreach ($tempbarcodes as $item) {

                        PromoGc::create([
                            'prom_promoid' => $data['promoNo'],
                            'prom_barcode' => $item->tp_barcode,
                            'prom_denom' => $item->tp_den,
                            'prom_gctype' => $item->tp_gctype,
                        ]);
                    }
                    return redirect(route('marketing.addPromo.list'));
                });
            }
            $response = ['msg' => 'Success!', 'description' => 'Promo successfully saved', 'type' => 'success'];
        }
        return response()->json(['response' => $response]);
    }


    public function gcpromoreleased(Request $request)
    {
        $response = [];
        $barcode = $request->data['barcode'];
        $dateReleased = Date::parse($request->data['dateReleased'])->format('Y-m-d');

        $promoGc = PromoGc::join('promo', 'promo.promo_id', '=', 'promo_gc.prom_promoid')
            ->where('promo_gc.prom_barcode', $barcode)
            ->select('promo_gc.*', 'promo.promo_dateexpire', 'promo_gc.pr_stat')
            ->first();

        if (!$promoGc) {
            return response()->json([
                'response' => [
                    'msg' => 'Oops!',
                    'description' => 'Promo GC not found for the given barcode.',
                    'type' => 'error'
                ]
            ]);
        }

        if ($promoGc->promo_dateexpire < $dateReleased) {
            $response = [
                'msg' => 'Oops!',
                'description' => 'Barcode Already Expired: ' . $promoGc->promo_dateexpire,
                'type' => 'error'
            ];
        } elseif ($promoGc->pr_stat == 1) {
            $response = [
                'msg' => 'Oops!',
                'description' => 'Promo GC Barcode #: ' . $barcode . ' already released',
                'type' => 'error'
            ];
        } else {

            $inserted = PromogcReleased::create([
                'prgcrel_barcode' => $barcode,
                'prgcrel_at' => $dateReleased,
                'prgcrel_by' => $request->data['releasedById'],
                'prgcrel_claimant' => $request->data['receiveBy'],
                'prgcrel_address' => $request->data['address'],
            ]);

            if ($inserted) {

                $lastInsertedId = $inserted->prgcrel_id;


                $ledgerNumber = LedgerBudget::orderByDesc('bledger_id')->first();
                $ln = optional($ledgerNumber)->bledger_no + 1;


                $denom = Gc::join('denomination', 'denomination.denom_id', '=', 'gc.denom_id')
                    ->where('gc.barcode_no', $barcode)
                    ->select('denomination.denomination')
                    ->first();

                if ($denom) {

                    $insertBudgetLedger = LedgerBudget::create([
                        'bledger_no' => $ln,
                        'bledger_trid' => $lastInsertedId,
                        'bledger_datetime' => Carbon::now()->format('Y-m-d H:i:s'),
                        'bledger_type' => 'PROMOGCRELEASING',
                        'bdebit_amt' => $denom->denomination,
                        'bledger_category' => 'regular',
                    ]);

                    if ($insertBudgetLedger) {

                        PromoGc::where('prom_barcode', $barcode)
                            ->update(['pr_stat' => 1]);
                        $response = [
                            'msg' => 'Success!',
                            'description' => 'Barcode Has Been Released',
                            'type' => 'success'
                        ];
                    } else {
                        $response = [
                            'msg' => 'Error!',
                            'description' => 'Failed to insert into LedgerBudget.',
                            'type' => 'error'
                        ];
                    }
                } else {
                    $response = [
                        'msg' => 'Error!',
                        'description' => 'Denomination not found for the given barcode.',
                        'type' => 'error'
                    ];
                }
            } else {
                $response = [
                    'msg' => 'Error!',
                    'description' => 'Failed to insert release record.',
                    'type' => 'error'
                ];
            }
        }


        return response()->json(['response' => $response]);
    }

    public function pendingRequest(Request $request)
    {
        $productionBarcode = self::productionRequest($request->id);

        $productionBarcode->transform(function ($item) {
            $item->total = $item->denomination * $item->pe_items_quantity;
            return $item;
        });



        $pendingRequests = ProductionRequest::where('pe_status', '0')
            ->join('users', 'users.user_id', '=', 'production_request.pe_requested_by')
            ->join('access_page', 'access_page.access_no', '=', 'users.usertype')
            ->get();

        $pendingRequests->transform(function ($item) {

            $dateReq = Date::parse($item->pe_date_request)->format('F d, Y');
            $requestId = $item->pe_id;
            $firstname = $item->firstname;
            $lastname = $item->lastname;
            $requestedBy = ucwords($firstname . ' ' . $lastname);
            $total = ProductionRequestItem::join('denomination', 'denomination.denom_id', '=', 'production_request_items.pe_items_denomination')
                ->where('production_request_items.pe_items_request_id', $requestId)
                ->select(DB::raw('SUM(denomination.denomination * production_request_items.pe_items_quantity) as total'))
                ->value('total');
            $item->dateReq = $dateReq;
            $item->requestedBy = $requestedBy;
            $item->total = $total;

            return $item;
        });

        $columns = array_map(
            fn($name, $field) => ColumnHelper::arrayHelper($name, $field),
            ['PR No', 'Date Request', 'Total Amount', 'Requested By', 'Department', 'Action'],
            ['pe_num', 'dateReq', 'total', 'requestedBy', 'title', 'View']
        );


        $barcodeColumns = array_map(
            fn($name, $field) => ColumnHelper::arrayHelper($name, $field),
            ['Denomination', 'Quantity', ''],
            ['denomination', 'pe_items_quantity', 'total']
        );



        return Inertia::render('Marketing/gcproductionrequest/PendingRequest', [
            'data' => $pendingRequests,
            'columns' => ColumnHelper::getColumns($columns),
            'barcodes' => $productionBarcode,
            'barcodeColumns' => ColumnHelper::getColumns($barcodeColumns),
        ]);
    }

    public function submitPendingRequest(Request $request)
    {
        $prid = $request->data['id'];
        $status = $request->data['status'];
        $userRole = $request->user()->user_role;

        switch ($status) {
            case 1:
                switch ($userRole) {
                    case 1:
                        $exists = ApprovedProductionRequest::where('ape_pro_request_id', $prid)->exists();
                        if ($exists) {
                            return back()->with([
                                'type' => 'warning',
                                'msg' => 'Warning!',
                                'description' => 'Production Pending Request Already Approved'
                            ]);
                        }
                        if ($this->marketing->handleManagerApproval($request, $prid)) {
                            return back()->with([
                                'type' => 'success',
                                'msg' => 'Success!',
                                'description' => 'The production pending request has been successfully approved.'
                            ]);
                        }
                        break;
                    case 0:
                        if (empty($request->data['InputRemarks'])) {
                            return back()->with([
                                'type' => 'error',
                                'msg' => 'Oops!',
                                'description' => 'Please fill all required fields'
                            ]);
                        }
                        if (empty($request->data['approvedBy'])) {
                            return back()->with([
                                'type' => 'error',
                                'msg' => 'Oops!',
                                'description' => 'Please contact authorized personnel to approve this production request first.'
                            ]);
                        }
                        if ($this->marketing->handleUserRole0Approval($request, $prid)) {
                            $this->RegularGc->approveProductionRequest($request, $prid);
                            $generated = $this->marketing->generateProductionRequestPDF($request);
                            if ($generated) {
                                return redirect()->back()->with([
                                    'type' => 'success',
                                    'stream' => base64_encode($generated->output())
                                ]);
                            }
                            return back()->with([
                                'type' => 'error',
                                'msg' => 'Oops!',
                                'description' => 'There is a problem generating the PDF.'
                            ]);
                        }
                        break;
                }
                break;
            case 2:
                if (empty($request->data['cancelremarks'])) {
                    return back()->with([
                        'type' => 'error',
                        'msg' => 'Oops!',
                        'description' => 'Kindly ensure that remarks are provided'
                    ]);
                }
                if ($this->marketing->handleRequestCancellation($request, $prid)) {
                    return back()->with([
                        'type' => 'success',
                        'msg' => 'Success!',
                        'description' => 'Production request successfully cancelled'
                    ]);
                }
                return back()->with([
                    'type' => 'warning',
                    'msg' => 'Warning!',
                    'description' => 'It appears that the pending request has already been canceled.'
                ]);
        }
    }




    public function approvedRequest(Request $request)
    {
        // dd();
        $query = $this->marketing->approvedProductionRequest($request);

        $columns = array_map(
            fn($name, $field) => ColumnHelper::arrayHelper($name, $field),
            ['PR No.', 'Date Request', 'Date Needed', '	Requested By', 'Date Approved', '	Approved By', ''],
            ['pe_num', 'dateReq', 'dateNeed', 'Reqprepared', 'ape_approved_at', 'ApprovedBy', 'View']
        );
        $selectedData = $this->marketing->approveProductionRequestSelectedData($request, $query);

        $productionBarcode = self::productionRequest($request->id);
        $barcodeColumns = array_map(
            fn($name, $field) => ColumnHelper::arrayHelper($name, $field),
            ['Denomination', 'Quantity', '	Barcode No. Start', '	Barcode No. End', 'Total'],
            ['denomination', 'pe_items_quantity', 'barcodeStart', 'barcodeEnd', 'total']
        );

        return Inertia::render('Marketing/gcproductionrequest/ApprovedRequest', [
            'data' => $query ?? [],
            'barcodes' => $productionBarcode,
            'columns' => ColumnHelper::getColumns($columns),
            'barcodeColumns' => ColumnHelper::getColumns($barcodeColumns),
            'selectedData' => $selectedData,
        ]);
    }

    public function promoPendinglist()
    {
        $data = $this->marketing->promoGcrequestPendingList();

        $columns = ColumnHelper::promopendinglistcols();

        return Inertia::render('Marketing/PromoGCRequest/PendingList', [
            'data' => $data,
            'columns' => $columns
        ]);
    }

    public function selectedPromoPendingRequest(Request $request)
    {
        $data = $this->marketing->selectedPromoPendingRequest($request);
        $denom = Denomination::where('denom_type', 'RSGC')
            ->where('denom_status', 'active')->get();


        $denomCol = ColumnHelper::denomCols();

        $denomQty = PromoGcRequestItem::where('pgcreqi_trid', $request->id)->get();
        $denom->transform(function ($item) use ($denomQty) {
            foreach ($denomQty as $denom) {
                if ($denom->pgcreqi_denom == $item->denom_id) {
                    $item->qty = $denom->pgcreqi_qty;
                }

            }

            return $item;

        });


        return Inertia::render('Marketing/PromoGCRequest/PendingGcRequestView', [
            'data' => $data,
            'denom' => $denom,
            'columns' => $denomCol,
            'denomQty' => $denomQty
        ]);
    }

    public function submitUpdate(Request $request)
    {
        $inserted = DB::transaction(function () use ($request) {
            PromoGcRequest::where('pgcreq_id', $request->reqnum)
                ->update([
                    'pgcreq_dateneeded' => Date::parse($request->dateNeed)->format('Y-m-d'),
                    'pgcreq_remarks' => $request->remarks,
                    'pgcreq_group' => $request->group,
                ]);
            $filteredArray = array_filter($request->denom, function ($item) {
                return isset($item['quantity']);
            });
            foreach ($filteredArray as $key => $value) {

                $promoGcRequestItem = PromoGcRequestItem::where('pgcreqi_trid', $request->reqnum)
                    ->where('pgcreqi_denom', $value['id']);
                if ($promoGcRequestItem->exists()) {
                    $promoGcRequestItem->update([
                        'pgcreqi_qty' => $value['quantity'],
                        'pgcreqi_remaining' => $value['quantity']
                    ]);
                } else {
                    PromoGcRequestItem::create([
                        'pgcreqi_trid' => $request->reqnum,
                        'pgcreqi_denom' => $value['id'],
                        'pgcreqi_qty' => $value['quantity'],
                        'pgcreqi_remaining' => $value['quantity']
                    ]);
                }
                if ($value['quantity'] == 0) {
                    PromoGcRequestItem::where('pgcreqi_trid', $request->reqnum)
                        ->where('pgcreqi_denom', $value['id'])->delete();
                }
            }
            return true;
        });

        if ($inserted) {
            return back()->with([
                'msg' => "Success!",
                'description' => "Promo GC Request Successfully Updated",
                'type' => "success",
            ]);
        } else {
            return back()->with([
                'msg' => "Opps!",
                'description' => "Something Went Wrong",
                'type' => "error",
            ]);
        }

    }

    public function addSupplier(Request $request)
    {
        $request->validate([
            'gcs_companyname' => 'required|string|max:255',
            'gcs_accountname' => 'required|string|max:255',
            'gcs_contactperson' => 'required|string|max:255',
            'gcs_contactnumber' => 'required|numeric',
            'gcs_address' => 'required|string|max:255',
        ]);


        $inserted = Supplier::create([
            'gcs_companyname' => $request['gcs_companyname'],
            'gcs_accountname' => $request['gcs_accountname'],
            'gcs_contactperson' => $request['gcs_contactperson'],
            'gcs_contactnumber' => $request['gcs_contactnumber'],
            'gcs_address' => $request['gcs_address'],
            'gcs_status' => '1',
        ]);

        if ($inserted) {
            return back()->with([
                'msg' => "Success!",
                'description' => "New Supplier Added ",
                'type' => "success",
            ]);
        } else {
            return back()->with([
                'msg' => "Opps!",
                'description' => "Something went wrong!",
                'type' => "error",
            ]);
        }
    }

    public function statusSupplier(Request $request)
    {
        $status = Supplier::where('gcs_id', $request->id)->first();
        Supplier::where('gcs_id', $request->id)
            ->update([
                'gcs_status' => $status->gcs_status == 0 ? 1 : 0
            ]);

        return back()->with([
            'msg' => "Status",
            'description' => "Status updated",
            'type' => "success",
        ]);

    }

    public function promoApprovedlist()
    {
        return Inertia::render('Marketing/PromoGCRequest/ApprovedGcRequest', [
            'data' => $this->marketing->promoApprovedlist()
        ]);
    }

    public function selectedApproved(Request $request)
    {
        return response()->json([
            'data' => $this->marketing->selectedpromogcrequest($request),
            'barcodes' => $this->marketing->getbarcode($request),
            'approvedRequests' => $this->marketing->approvedRequests($request)
        ]);

    }

    public function checkerpendingRequest(Request $request)
    {
        $productionBarcode = self::productionRequest($request->id);

        $productionBarcode->transform(function ($item) {
            $item->total = $item->denomination * $item->pe_items_quantity;
            return $item;
        });



        $pendingRequests = ProductionRequest::where('pe_status', '0')
            ->join('users', 'users.user_id', '=', 'production_request.pe_requested_by')
            ->join('access_page', 'access_page.access_no', '=', 'users.usertype')
            ->get();

        $pendingRequests->transform(function ($item) {

            $dateReq = Date::parse($item->pe_date_request)->format('F d, Y');
            $dateneed = Date::parse($item->pe_date_needed)->format('F d, Y');
            $requestId = $item->pe_id;
            $firstname = $item->firstname;
            $lastname = $item->lastname;
            $requestedBy = ucwords($firstname . ' ' . $lastname);
            $total = ProductionRequestItem::join('denomination', 'denomination.denom_id', '=', 'production_request_items.pe_items_denomination')
                ->where('production_request_items.pe_items_request_id', $requestId)
                ->select(DB::raw('SUM(denomination.denomination * production_request_items.pe_items_quantity) as total'))
                ->value('total');
            $item->dateReq = $dateReq;
            $item->dateneed = $dateneed;
            $item->requestedBy = $requestedBy;
            $item->total = $total;

            return $item;
        });


        $columns = array_map(
            fn($name, $field) => ColumnHelper::arrayHelper($name, $field),
            ['PR No', 'Date Request', 'Total Amount', 'Date Needed', 'Requested By', 'Department', 'Action'],
            ['pe_num', 'dateReq', 'total', 'dateneed', 'requestedBy', 'title', 'View']
        );


        $barcodeColumns = array_map(
            fn($name, $field) => ColumnHelper::arrayHelper($name, $field),
            ['Denomination', 'Quantity', ''],
            ['denomination', 'pe_items_quantity', 'total']
        );



        return Inertia::render('Marketing/gcproductionrequest/CheckerPendingRequest', [
            'data' => $pendingRequests,
            'columns' => ColumnHelper::getColumns($columns),
            'barcodes' => $productionBarcode,
            'barcodeColumns' => ColumnHelper::getColumns($barcodeColumns)

        ]);
    }

    public function approvependingRequest(Request $request)
    {


        $productionBarcode = self::productionRequest($request->id);

        $productionBarcode->transform(function ($item) {
            $item->total = $item->denomination * $item->pe_items_quantity;
            return $item;
        });



        $pendingRequests = ProductionRequest::where('pe_status', '0')
            ->join('users', 'users.user_id', '=', 'production_request.pe_requested_by')
            ->join('access_page', 'access_page.access_no', '=', 'users.usertype')
            ->get();

        $pendingRequests->transform(function ($item) {

            $dateReq = Date::parse($item->pe_date_request)->format('F d, Y');
            $dateneed = Date::parse($item->pe_date_needed)->format('F d, Y');
            $requestId = $item->pe_id;
            $firstname = $item->firstname;
            $lastname = $item->lastname;
            $requestedBy = ucwords($firstname . ' ' . $lastname);
            $total = ProductionRequestItem::join('denomination', 'denomination.denom_id', '=', 'production_request_items.pe_items_denomination')
                ->where('production_request_items.pe_items_request_id', $requestId)
                ->select(DB::raw('SUM(denomination.denomination * production_request_items.pe_items_quantity) as total'))
                ->value('total');
            $item->dateReq = $dateReq;
            $item->dateneed = $dateneed;
            $item->requestedBy = $requestedBy;
            $item->total = $total;

            return $item;
        });


        $columns = array_map(
            fn($name, $field) => ColumnHelper::arrayHelper($name, $field),
            ['PR No', 'Date Request', 'Total Amount', 'Date Needed', 'Requested By', 'Department', 'Action'],
            ['pe_num', 'dateReq', 'total', 'dateneed', 'requestedBy', 'title', 'View']
        );


        $barcodeColumns = array_map(
            fn($name, $field) => ColumnHelper::arrayHelper($name, $field),
            ['Denomination', 'Quantity', ''],
            ['denomination', 'pe_items_quantity', 'total']
        );



        return Inertia::render('Marketing/gcproductionrequest/approvePendingRequest', [
            'data' => $pendingRequests,
            'columns' => ColumnHelper::getColumns($columns),
            'barcodes' => $productionBarcode,
            'barcodeColumns' => ColumnHelper::getColumns($barcodeColumns)
        ]);
    }


    public function getSigners(Request $request)
    {
        if ($request->data) {
            $q = $request->data;
        } else {
            $q = $request['id'];
        }
        $query = ApprovedProductionRequest::where('ape_pro_request_id', $q)->first();


        $approvedBy = UserDetails::where('user_id', $query['ape_approved_by'])->first();

        $data = [
            'approvedBy' => $approvedBy['details'],
            'approvedById' => $approvedBy['user_id'],

        ];
        return response()->json([
            'response' => $data
        ]);
    }

    public function getChecker(Request $request)
    {
        $query = ApprovedProductionRequest::where('ape_pro_request_id', $request->data)->first();
        $q = UserDetails::where('user_id', $query->ape_checked_by)->first();

        return response()->json([
            'checkedBy' => $q['details']['employee_name'],
            'checkedById' => $query['ape_checked_by'],
        ]);
    }

    public function cancelledProductionRequest()
    {
        $data = ProductionRequest::where('pe_status', '2')
            ->select(
                'production_request.*',
                'requestedBy.firstname as requested_by_firstname',
                'requestedBy.lastname as requested_by_lastname',
                'cancelled_production_request.*',
                'cancelledBy.firstname as cancelled_by_firstname',
                'cancelledBy.lastname as cancelled_by_lastname',
            )
            ->leftJoin('users as requestedBy', 'requestedBy.user_id', '=', 'production_request.pe_requested_by')
            ->leftJoin('cancelled_production_request', 'cancelled_production_request.cpr_pro_id', '=', 'production_request.pe_id')
            ->leftJoin('users as cancelledBy', 'cancelledBy.user_id', '=', 'cancelled_production_request.cpr_by')
            ->orderByDesc('pe_id')
            ->paginate()
            ->withQueryString();

        $data->transform(function ($item) {
            $item->requestedBy = ucwords($item->requested_by_firstname . ' ' . $item->requested_by_lastname);
            $item->cancelledBy = ucwords($item->cancelled_by_firstname . ' ' . $item->cancelled_by_lastname);
            $item->Daterequested = Date::parse($item->pe_date_request)->format('F d, Y');
            $item->dateCancelled = $item->cpr_at ? Date::parse($item->cpr_at)->format('F d, Y') : '';
            return $item;
        });

        return inertia('Marketing/gcproductionrequest/CancelledPR', [
            'data' => $data
        ]);
    }

    public function ViewcancelledProductionRequest(Request $request)
    {
        $data = ProductionRequest::where('pe_status', '2')
            ->select(
                'production_request.*',
                'requestedBy.firstname as requested_by_firstname',
                'requestedBy.lastname as requested_by_lastname',
                'cancelled_production_request.*',
                'cancelledBy.firstname as cancelled_by_firstname',
                'cancelledBy.lastname as cancelled_by_lastname',
            )
            ->where('production_request.pe_id', $request->id)
            ->leftJoin('users as requestedBy', 'requestedBy.user_id', '=', 'production_request.pe_requested_by')
            ->leftJoin('cancelled_production_request', 'cancelled_production_request.cpr_pro_id', '=', 'production_request.pe_id')
            ->leftJoin('users as cancelledBy', 'cancelledBy.user_id', '=', 'cancelled_production_request.cpr_by')
            ->orderByDesc('pe_id')
            ->get();

        $data->transform(function ($item) {
            $item->requestedBy = ucwords($item->requested_by_firstname . ' ' . $item->requested_by_lastname);
            $item->cancelledBy = ucwords($item->cancelled_by_firstname . ' ' . $item->cancelled_by_lastname);
            $item->Daterequested = Date::parse($item->pe_date_request)->format('F d, Y');
            $item->dateCancelled = $item->cpr_at ? Date::parse($item->cpr_at)->format('F d, Y') : '';
            return $item;
        });
        return response()->json([
            'response' => $data
        ]);
    }

    public function promocancelledlist(Request $request)
    {

        $data = PromoGcRequest::where('pgcreq_group_status', 'cancelled')
            ->leftJoin('users as requestBy', 'requestBy.user_id', '=', 'promo_gc_request.pgcreq_reqby')
            ->leftJoin('users', 'users.user_id', '=', 'promo_gc_request.pgcreq_updateby')
            ->whereAny([
                'pgcreq_reqnum',
                'pgcreq_reqby',
                'pgcreq_updateby'
            ], 'like', $request->search . '%')
            ->select('promo_gc_request.*', 'users.firstname', 'users.lastname', 'requestBy.firstname as requestedByName', 'requestBy.lastname as requestedBylastname')
            ->paginate()
            ->withQueryString();

        $data->transform(function ($item) {
            $item->cancelledBy = ucwords($item->firstname . ' ' . $item->lastname);
            $item->requestedBy = ucwords($item->requestedByName . ' ' . $item->requestedBylastname);
            return $item;
        });

        return Inertia::render('Marketing/PromoGCRequest/CancelledRequest', [
            'data' => $data
        ]);
    }

    public function pendingspgclist()
    {
        $pending = $this->marketing->countspecialgc();
        $internal = $pending['pending']['internal'];
        $external = $pending['pending']['external'];

        return inertia('Marketing/specialgc/Pending', [
            'internal' => $internal,
            'external' => $external
        ]);
    }

    public function pendingspgclistview(Request $request)
    {

        $data = $this->marketing->viewspecialgc($request->type, $request->id);
        $denom = SpecialExternalGcrequestItem::where('specit_trid', $request->id)->get();
        $doc = Document::where('doc_trid', $request->id)
            ->where('doc_type', 'Special External GC Request')
            ->select('doc_fullpath')
            ->first();

        return response()->json([
            'data' => $data,
            'denom' => $denom,
            'doc' => $doc['doc_fullpath'] ?? null
        ]);
    }

    public function submitReqForm(Request $request)
    {
        if ($request->data['finalize'] == 1) {

            if (
                $request->data['id'] == null ||
                $request->data['requestNo'] == null ||
                $request->data['finalize'] == null ||
                $request->data['productionReqNum'] == null ||
                $request->data['dateRequested'] == null ||
                $request->data['location'] == null ||
                $request->data['department'] == null ||
                $request->data['remarks'] == null ||
                $request->data['checkedBy'] == null ||
                $request->data['approvedById'] == null ||
                $request->data['approvedBy'] == null ||
                $request->data['selectedSupplierId'] == null ||
                $request->data['contactPerson'] == null ||
                $request->data['contactNum'] == null ||
                $request->data['address'] == null
            ) {
                return back()->with([
                    'msg' => "Select",
                    'description' => "Please fill all required fields",
                    'type' => "error",
                ]);
            } else {
                $lnumber = LedgerCheck::count() + 1;
                $reqtotal = ProductionRequestItem::join('denomination', 'denomination.denom_id', '=', 'production_request_items.pe_items_denomination')
                    ->where('production_request_items.pe_items_request_id', $request->data['id'])
                    ->selectRaw('IFNULL(SUM(production_request_items.pe_items_quantity * denomination.denomination), 0) as total')
                    ->value('total');

                $inserted = DB::transaction(function () use ($request, $lnumber, $reqtotal) {
                    $ledgerCheck = LedgerCheck::create([
                        'cledger_no' => $lnumber,
                        'cledger_datetime' => now(),
                        'cledger_type' => 'GCRA',
                        'cledger_desc' => 'GC Requisition Approved',
                        'cdebit_amt' => $reqtotal,
                        'c_posted_by' => $request->user()->user_id,
                    ]);

                    $requisEntry = RequisitionEntry::create([
                        'requis_erno' => $request->data['requestNo'],
                        'requis_req' => now()->format('Y-m-d'),
                        'requis_need' => null,
                        'requis_loc' => $request->data['location'],
                        'requis_dept' => $request->data['department'],
                        'requis_rem' => $request->data['remarks'],
                        'repuis_pro_id' => $request->data['id'],
                        'requis_req_by' => $request->user()->user_id,
                        'requis_checked' => $request->data['checkedBy'],
                        'requis_supplierid' => $request->data['selectedSupplierId'],
                        'requis_ledgeref' => $lnumber,
                        'requis_approved' => $request->data['approvedBy'],
                        'requis_foldersaved' => ''
                    ]);

                    return [
                        'legderCheck' => $ledgerCheck,
                        'requisEntry' => $requisEntry,
                    ];
                });

                if ($inserted) {

                    $pdfgenerated = $this->marketing->generatepdfrequisition($request);
                    if ($pdfgenerated) {
                        ProductionRequest::where('pe_id', $request->data['id'])
                            ->update([
                                'pe_requisition' => '1'
                            ]);

                        return redirect()->back()->with([
                            'type' => 'success',
                            'stream' => base64_encode($pdfgenerated->output())
                        ]);
                    }
                }
            }
        } elseif ($request->data['finalize'] == 3) {


            $total = 0;
            $lnumber = LedgerCheck::count() + 1;
            $productionDetails = ProductionRequest::where('pe_id', $request->data['id'])
                ->select(['pe_type', 'pe_group'])
                ->get();
            $ptype = $productionDetails[0]->pe_type;
            $pgroup = $productionDetails[0]->pe_group;


            if ($productionDetails) {
                $updateProductionStatus = ProductionRequest::where('pe_id', $request->data['id'])
                    ->update([
                        'pe_requisition' => '2'
                    ]);

                if ($updateProductionStatus) {
                    $amount = ProductionRequestItem::join('denomination', 'production_request_items.pe_items_denomination', '=', 'denomination.denom_id')
                        ->where('pe_items_request_id', $request->data['id'])->get();

                    foreach ($amount as $item) {
                        $sub = $item->pe_items_quantity * $item->denomination;
                        $total = $total + $sub;
                    }
                    $cancelGc = Gc::where('pe_entry_gc', $request->data['id'])
                        ->update(['gc_cancelled' => '*']);


                    if ($cancelGc) {


                        $isInserted = LedgerBudget::create([
                            'bledger_no' => $lnumber,
                            'bledger_datetime' => now(),
                            'bledger_type' => 'RC',
                            'bledger_trid' => '0',
                            'bledger_typeid' => $ptype,
                            'bledger_group' => $pgroup,
                            'bdebit_amt' => $total,
                            'bledger_category' => 'regular'
                        ]);


                        if ($isInserted) {

                            $cancelled = CancelledProductionRequest::create([
                                'cpr_pro_id' => $request->data['id'],
                                'cpr_isrequis_cancel' => '1',
                                'cpr_ldgerid' => $isInserted->bledger_id,
                                'cpr_at' => now(),
                                'cpr_by' => $request->user()->user_id
                            ]);

                            if ($cancelled) {
                                return back()->with([
                                    'type' => 'success',
                                    'msg' => 'Success',
                                    'description' => 'Production Request Successfully Cancelled'
                                ]);
                            }

                        }
                    }

                }
            }
        }
    }

    public function ApprovedExternalGcRequest(Request $request)
    {

        $approveExtGCReq = $this->marketing->approvedSpecialExternalRequest($request->search);

        return inertia('Marketing/specialgc/Approved', [
            'apexgcreq' => $approveExtGCReq
        ]);
    }

    public function selectedApprovedExternalGcRequest(Request $request)
    {


        $query = SpecialExternalGcrequest::select(
            'special_external_gcrequest.spexgc_id',
            'special_external_gcrequest.spexgc_num',
            DB::raw("CONCAT(req.firstname, ' ', req.lastname) as reqby"),
            'special_external_gcrequest.spexgc_datereq',
            'special_external_gcrequest.spexgc_dateneed',
            'special_external_gcrequest.spexgc_remarks',
            'special_external_gcrequest.spexgc_payment',
            'special_external_gcrequest.spexgc_paymentype',
            'special_external_gcrequest.spexgc_payment_arnum',
            'special_external_customer.spcus_companyname',
            'approved_request.reqap_remarks',
            'approved_request.reqap_doc',
            'approved_request.reqap_checkedby',
            'approved_request.reqap_approvedby',
            'approved_request.reqap_preparedby',
            'approved_request.reqap_date',
            DB::raw("CONCAT(prep.firstname, ' ', prep.lastname) as prepby")
        )
            ->join('users as req', 'req.user_id', '=', 'special_external_gcrequest.spexgc_reqby')
            ->join('special_external_customer', 'special_external_customer.spcus_id', '=', 'special_external_gcrequest.spexgc_company')
            ->join('approved_request', 'approved_request.reqap_trid', '=', 'special_external_gcrequest.spexgc_id')
            ->join('users as prep', 'prep.user_id', '=', 'approved_request.reqap_preparedby')
            ->where('special_external_gcrequest.spexgc_status', 'approved')
            ->where('special_external_gcrequest.spexgc_id', $request->id)
            ->where('approved_request.reqap_approvedtype', 'Special External GC Approved')
            ->get();
        $query->transform(function ($item) {
            $item->preparedby = ucwords($item->prepby);
            $item->checkedby = ucwords($item->reqap_checkedby);
            $item->approveby = ucwords($item->reqap_approvedby);
            $item->requestedby = ucwords($item->reqby);
            $item->datedRequested = Date::parse($item->spexgc_datereq)->format('F d, Y');
            $item->datedValidity = Date::parse($item->spexgc_dateneed)->format('F d, Y');
            $item->datedApproved = Date::parse($item->reqap_date)->format('F d, Y');

            return $item;
        });

        $barcodes = SpecialExternalGcrequestEmpAssign::where('spexgcemp_trid', $request->id)->get();
        $barcodes->transform(function ($item) {
            $item->holderfullname = ucwords($item->spexgcemp_fname . ' ' . $item->spexgcemp_mname . ' ' . $item->spexgcemp_lname);
            return $item;
        });


        return response()->json([
            'data' => $query,
            'barcodes' => $barcodes
        ]);
    }

    public function getrequisition(Request $request)
    {
        $query = RequisitionEntry::select(
            'requisition_entry.requis_erno',
            'requisition_entry.requis_req',
            'production_request.pe_date_needed',
            'requisition_entry.requis_loc',
            'requisition_entry.requis_dept',
            'requisition_entry.requis_rem',
            'requisition_entry.requis_checked',
            'requisition_entry.requis_approved',
            'supplier.gcs_companyname',
            'supplier.gcs_contactperson',
            'supplier.gcs_contactnumber',
            'supplier.gcs_address',
            'users.firstname',
            'users.lastname'
        )
            ->join('production_request', 'requisition_entry.repuis_pro_id', '=', 'production_request.pe_id')
            ->join('supplier', 'requisition_entry.requis_supplierid', '=', 'supplier.gcs_id')
            ->join('users', 'users.user_id', '=', 'requisition_entry.requis_req_by')
            ->where('requisition_entry.repuis_pro_id', $request->id)
            ->first();

        return response()->json([
            'r' => $query ?? null
        ]);
    }

    public function reprint(Request $request)
    {
        $filePath = Storage::path('public/e-requisitionform/' . $request->id . '.pdf');

        if (!file_exists($filePath)) {
            return response()->json([
                'stream' => null
            ]);
        }
        $fileContent = file_get_contents($filePath);

        return response()->json([
            'stream' => base64_encode($fileContent)
        ]);
    }

    public function countreleasedspexgc()
    {
        return response()->json(['count' => SpecialExternalGcrequest::where('spexgc_released', 'released')->count()]);
    }

    public function releasedspexgc()
    {
        $query = SpecialExternalGcrequest::join('users as req', 'req.user_id', '=', 'special_external_gcrequest.spexgc_reqby')
            ->join('special_external_customer', 'special_external_customer.spcus_id', '=', 'special_external_gcrequest.spexgc_company')
            ->join('approved_request', 'approved_request.reqap_trid', '=', 'special_external_gcrequest.spexgc_id')
            ->join('users as rev', 'rev.user_id', '=', 'approved_request.reqap_preparedby')
            ->where('special_external_gcrequest.spexgc_released', '=', 'released')
            ->where('approved_request.reqap_approvedtype', '=', 'special external releasing')
            ->select([
                'special_external_gcrequest.spexgc_id',
                'special_external_gcrequest.spexgc_num',
                DB::raw("CONCAT(req.firstname, ' ', req.lastname) as reqby"),
                'special_external_gcrequest.spexgc_datereq',
                'special_external_gcrequest.spexgc_dateneed',
                'special_external_customer.spcus_acctname',
                'special_external_customer.spcus_companyname',
                'approved_request.reqap_date',
                DB::raw("CONCAT(rev.firstname, ' ', rev.lastname) as revby")
            ])
            ->orderByDesc('spexgc_id')
            ->paginate()
            ->withQueryString();
        $query->transform(function ($item) {
            $item->datereq = Date::parse($item->spexgc_datereq)->format('F d, Y');
            $item->requestedBy = ucwords($item->reqby);
            $item->releasedBy = ucwords($item->revby);
            return $item;
        });
        return inertia('Marketing/specialgc/ReleasedSpexGc', [
            'data' => $query
        ]);
    }

    public function viewReleasedSpexGc(Request $request)
    {
        return response()->json([
            'data' => $this->marketing->viewReleasedSpexGc($request)
        ]);
    }

    public function cancelledspexgclist(Request $request)
    {
        return inertia('Marketing/specialgc/Cancelledspexgc', [
            'data' => SpecialExternalGcrequest::where('spexgc_status', 'cancelled')
                ->join('special_external_customer', 'special_external_customer.spcus_id', 'special_external_gcrequest.spexgc_company')
                ->whereAny([
                    'spexgc_id',
                ], 'like', $request->data . '%')
                ->paginate()
                ->withQueryString()
        ]);
    }

    public function view(Request $request)
    {
        $data = $this->marketing->view($request);
        return inertia('Marketing/ViewTransaction/View', [
            'data' => $data,
            'returnRoute' => $request['store_url']
        ]);
    }
}
