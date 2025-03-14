<?php

namespace App\Services\Admin;

use App\Helpers\NumberHelper;
use App\Models\Denomination;
use App\Models\DtiBarcodes;
use App\Models\Gc;
use App\Models\InstitutTransactionsItem;
use App\Models\PromoGcReleaseToItem;
use App\Models\RequisitionForm;
use App\Models\SpecialExternalGcrequestEmpAssign;
use App\Models\Store;
use App\Models\Supplier;
use App\Models\TransactionStore;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\Date;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\Str;

class AdminServices
{

    public function __construct(public DBTransaction $dBTransaction) {}

    public function purchaseOrderDetails()
    {


        $newFolder  = Storage::disk('fad')->files('New');

        $files = array_map('basename', $newFolder);

        $lazyFiles = LazyCollection::make(function () use ($files) {
            foreach ($files as $file) {
                yield $file;
            }
        });

        return $lazyFiles;
    }

    public function denomination()
    {
        return Denomination::where('denom_status', 'active')->select('denom_id', 'denom_fad_item_number', 'denomination')->get();
    }

    public function statusScanned(Request $request)
    {
        $regular = new Gc();
        $special = new SpecialExternalGcrequestEmpAssign();
        $specialDti = new DtiBarcodes();
        $promo = new PromoGcReleaseToItem();
        $inst = new InstitutTransactionsItem();
        $barcodeNotFound = false;
        $empty = false;
        $steps = [];
        $transType = '';
        $success = false;


        if (
            $regular->where('barcode_no', $request->barcode)->exists()
            && !$regular->whereHas('barcodePromo', fn($query) => $query->where('barcode_no', $request->barcode))->exists()
            && !$regular->whereHas('barcodeInst', fn($query) => $query->where('barcode_no', $request->barcode))->exists()
        ) {

            $transType = 'Reqular Gift Check';
            $steps = self::regularGc($regular, $request);
            $success = true;
        } elseif (
            $special->where('spexgcemp_barcode', $request->barcode)
            ->where('spexgcemp_barcode', '!=', '0')
            ->exists()
        ) {
            $transType = 'Special Gift Check';
            $steps = self::specialStatus($special, $request);
            $success = true;
        } elseif ($promo->where('prreltoi_barcode', $request->barcode)->exists()) {
            $transType = 'Promo Gift Check';
            $steps = self::promoStatus($promo, $request);
            $success = true;
        } elseif ($inst->where('instituttritems_barcode', $request->barcode)->exists()) {
            $transType = 'Institutional Gift Check';
            $steps = self::institutionStatus($inst, $request);
            $success = true;
        }elseif($specialDti->where('dti_barcode', $request->barcode)
        ->where('dti_barcode', '!=', '0')
        ->exists()){
            $transType = 'Special Gift Check';
            $steps = self::specialStatusDti($specialDti, $request);
            $success = true;
        }elseif (empty($request->barcode)) {
            $empty = true;
        } else {
            $barcodeNotFound = true;
        }
        // dd($steps);

        return (object) [
            'steps' => $steps,
            'transType' => $transType,
            'success' => $success,
            'barcodeNotFound' => $barcodeNotFound,
            'empty' => $empty,
        ];
    }

    public static function regularGc($step3, $request)
    {
        //Treasury & Marketing

        $steps = collect([
            [
                'title' => 'Treasury',
                'status' => 'finish',
                'description' => 'Request Submitted'
            ],
            [
                'title' => 'Marketing',
                'status' => 'finish',
                'description' => 'Request Approved'
            ]
        ]);

        // dd($step3->exists());

        if ($step3->whereHas('barcode', fn($query) => $query->where('barcode_no', $request->barcode))->exists()) {
            $steps->push((object) [
                'title' => 'FAD',
                'description' => 'Scanned By FAD'
            ]);
        } else {
            $steps->push((object) [
                'status' => 'error',
                'title' => 'FAD',
                'description' => 'Not Scanned By FAD'
            ]);
        }


        if ($step3->whereHas('iadBarcode', fn($query) => $query->where('cssitem_barcode', $request->barcode))->exists()) {
            $steps->push((object) [
                'title' => 'IAD',
                'description' => 'Scanned By IAD'
            ]);
        } else {
            $steps->push((object) [
                'status' => 'error',
                'title' => 'IAD',
                'description' => 'Not Scanned By IAD'
            ]);
        }
        // dd($step3->where('barcode_no', $request->barcode)->where('gc_validated', '*')->exists());
        if ($step3->where('barcode_no', $request->barcode)->where('gc_validated', '*')->exists()) {
            $steps->push((object) [
                'title' => 'Validated',
                'description' => 'Validated By IAD'
            ]);
        } else {
            $steps->push((object) [
                'status' => 'error',
                'title' => 'Validated',
                'description' => 'Not Validated By IAD'
            ]);
        }
        // $step3->where('gc_allocated', '*')
        if ($step3->where('barcode_no', $request->barcode)->where('gc_allocated', '*')->exists()) {
            $steps->push((object) [
                'title' => 'Allocated',
                'description' => 'Allocated By Treasury'
            ]);
        } else {
            $steps->push((object) [
                'status' => 'error',
                'title' => 'Not Allocated',
                'description' => 'Not yet Allocated By Treasury'
            ]);
        }
        if ($step3->whereHas('treasuryCfsBarcode', fn($query) => $query->where('strec_barcode', $request->barcode))->exists()) {
            $steps->push((object) [
                'title' => 'Transfered',
                'description' => 'Transfered From Treasury To CFS'
            ]);
        } else {
            $steps->push((object) [
                'status' => 'error',
                'title' => 'Transfered',
                'description' => 'Not yet Transfered By Treasury To CFS'
            ]);
        }
        $q = $step3->join('store_received_gc', 'store_received_gc.strec_barcode', '=', 'gc.barcode_no')
            ->where('store_received_gc.strec_barcode', $request->barcode);
        $res =  $q->where('strec_sold', '*')->exists();
        if ($res) {
            $steps->push((object) [
                'title' => 'Sold',
                'description' => 'Sold to Customer'
            ]);
        } else {
            $steps->push((object) [
                'status' => 'error',
                'title' => 'Sold',
                'description' => 'Not yet sold',

            ]);
        }
        $q2 =  $step3->join('store_verification', 'store_verification.vs_barcode', '=', 'gc.barcode_no')
            ->where('vs_barcode', $request->barcode);

        $vs_date = $q2->first()->vs_date ?? null;
        $rev_date = $q2->first()->vs_reverifydate ?? null;
        $vs_pay = $q2->first()->vs_payto ?? null;

        if ($step3->whereHas('reverified', fn($query) => $query->where('vs_barcode', $request->barcode))->exists()) {

            $steps->push((object) [
                'title' => 'Verification',
                'description' => 'Verified By CFS ' . ' at ' . Date::parse($vs_date)->toFormattedDateString(),
            ]);
        } else {
            $steps->push((object) [
                'status' => 'error',
                'title' => 'Verification',
                'description' => 'Not yet Verified By CFS',

            ]);
        }

        $isRevDateExists = $q2->where('barcode_no', $request->barcode)->where('vs_reverifydate', $rev_date)->exists();

        $isRevDateNull = $q2->where('barcode_no', $request->barcode)->where('vs_reverifydate', null)->exists();

        if ($isRevDateNull) {
            //no result here..
        } elseif ($isRevDateExists) {

            $steps->push((object) [
                'title' => 'Reverification',
                'description' => 'Reverified By CFS at ' . Date::parse($rev_date)->toFormattedDateString(),
            ]);
        } else {
            //no result here..
        }

        $q3 =  $step3->join('store_verification', 'store_verification.vs_barcode', '=', 'gc.barcode_no')->where('vs_barcode', $request->barcode);
        if ($q3->where('vs_tf_used', '*')->exists()) {
            $steps->push((object) [
                'status' => 'finish',
                'title' => 'Redeemption',
                'description' => 'Redeemed by Customer at ' . $vs_pay,
            ]);
        } else {
            $steps->push((object) [
                'status' => 'error',
                'title' => 'Redeemption',
                'description' => 'Not yet Redeem',
            ]);
        }

        return $steps;
    }

    public static function specialStatus($special, $request)
    {
        //Treasury & Marketing
        $steps = collect([
            [
                'title' => 'Treasury',
                'status' => 'finish',
                'description' => 'Request Submitted'
            ],
            [
                'title' => 'FAD',
                'status' => 'finish',
                'description' => 'Request Approved'
            ],
            [
                'title' => 'Finance',
                'status' => 'finish',
                'description' => 'Generated Barcode Success'
            ]
        ]);

        if ($special->where('spexgcemp_barcode', $request->barcode)->where('spexgcemp_review', '*')->exists()) {
            $steps->push((object) [
                'title' => 'IAD',
                'description' => 'Scanned by IAD'
            ]);
        } else {
            $steps->push((object) [
                'status' => 'error',
                'title' => 'IAD',
                'description' => 'Not Scanned by IAD'
            ]);
        }
        $data = $special->join('store_verification', 'store_verification.vs_barcode', '=', 'special_external_gcrequest_emp_assign.spexgcemp_barcode')->where('vs_barcode', $request->barcode);;

        // dd($data->first()->vs_date);
        if ($special->whereHas('reverified', fn($query) => $query->where('vs_barcode', $request->barcode))->exists()) {
            $steps->push((object) [
                'title' => 'Verification',
                'description' => 'Verified By CFS ' . ' at ' . Date::parse($data->first()->vs_date)->toFormattedDateString(),
            ]);
        } else {
            $steps->push((object) [
                'status' => 'error',
                'title' => 'Verification',
                'description' => 'Not yet Verified By CFS',

            ]);
        }

        $rev_date = $data->first()->vs_reverifydate ?? null;

        if ($data->where('vs_barcode', $request->barcode)->whereNotNull('vs_reverifydate')->exists()) {
            $steps->push((object) [
                'title' => 'Reverification',
                'description' => 'Reverified By CFS at ' . Date::parse($rev_date)->toFormattedDateString(),
            ]);
        } else {
        }
        $data2 = $special->join('store_verification', 'store_verification.vs_barcode', '=', 'special_external_gcrequest_emp_assign.spexgcemp_barcode')->where('vs_barcode', $request->barcode);;

        if ($data2->where('vs_barcode', $request->barcode)->where('vs_tf_used', '*')->exists()) {

            $steps->push((object) [
                'status' => 'finish',
                'title' => 'Redeemption',
                'description' => 'Redeemed by Customer at ' . $data2->first()->vs_payto,
            ]);
        } else {
            $steps->push((object) [
                'status' => 'error',
                'title' => 'Redeemption',
                'description' => 'Not yet Redeem',
            ]);
        }


        return $steps;
    }
    public static function specialStatusDti($special, $request)
    {
        //Treasury & Marketing
        $steps = collect([
            [
                'title' => 'Treasury',
                'status' => 'finish',
                'description' => 'Request Submitted'
            ],
            [
                'title' => 'FAD',
                'status' => 'finish',
                'description' => 'Request Approved'
            ],
            [
                'title' => 'Finance',
                'status' => 'finish',
                'description' => 'Generated Barcode Success'
            ]
        ]);

        if ($special->where('dti_barcode', $request->barcode)->where('dti_review', '*')->exists()) {
            $steps->push((object) [
                'title' => 'IAD',
                'description' => 'Scanned by IAD'
            ]);
        } else {
            $steps->push((object) [
                'status' => 'error',
                'title' => 'IAD',
                'description' => 'Not Scanned by IAD'
            ]);
        }
        $data = $special->join('store_verification', 'store_verification.vs_barcode', '=', 'dti_barcodes.dti_barcode')->where('vs_barcode', $request->barcode);

        // dd($data->first()->vs_date);
        if ($special->whereHas('reverified', fn($query) => $query->where('vs_barcode', $request->barcode))->exists()) {
            $steps->push((object) [
                'title' => 'Verification',
                'description' => 'Verified By CFS ' . ' at ' . Date::parse($data->first()->vs_date)->toFormattedDateString(),
            ]);
        } else {
            $steps->push((object) [
                'status' => 'error',
                'title' => 'Verification',
                'description' => 'Not yet Verified By CFS',

            ]);
        }

        $rev_date = $data->first()->vs_reverifydate ?? null;

        if ($data->where('vs_barcode', $request->barcode)->whereNotNull('vs_reverifydate')->exists()) {
            $steps->push((object) [
                'title' => 'Reverification',
                'description' => 'Reverified By CFS at ' . Date::parse($rev_date)->toFormattedDateString(),
            ]);
        } else {
        }
        $data2 = $special->join('store_verification', 'store_verification.vs_barcode', '=', 'dti_barcodes.dti_barcode')->where('vs_barcode', $request->barcode);;

        if ($data2->where('vs_barcode', $request->barcode)->where('vs_tf_used', '*')->exists()) {

            $steps->push((object) [
                'status' => 'finish',
                'title' => 'Redeemption',
                'description' => 'Redeemed by Customer at ' . $data2->first()->vs_payto,
            ]);
        } else {
            $steps->push((object) [
                'status' => 'error',
                'title' => 'Redeemption',
                'description' => 'Not yet Redeem',
            ]);
        }


        return $steps;
    }
    public static function promoStatus($promo, $request)
    {

        $steps = collect([
            [
                'title' => 'Treasury',
                'status' => 'finish',
                'description' => 'Request Submitted'
            ],
            [
                'title' => 'Marketing',
                'status' => 'finish',
                'description' => 'Request Approved'
            ]
        ]);

        if ($promo->exists()) {
            $steps->push((object) [
                'title' => 'FAD',
                'description' => 'Scanned By FAD'
            ]);
        } else {
            $steps->push((object) [
                'status' => 'error',
                'title' => 'FAD',
                'description' => 'Not Scanned By FAD'
            ]);
        }


        if ($promo->whereHas('iadBarcode', fn($query) => $query->where('cssitem_barcode', $request->barcode))->exists()) {
            $steps->push((object) [
                'title' => 'IAD',
                'description' => 'Scanned By IAD'
            ]);
        } else {
            $steps->push((object) [
                'status' => 'error',
                'title' => 'IAD',
                'description' => 'Not Scanned By IAD'
            ]);
        }
        if ($promo->join('gc', 'gc.barcode_no', '=', 'prreltoi_barcode')->where('barcode_no', $request->barcode)->where('gc_ispromo', '*')->exists()) {
            $steps->push((object) [
                'title' => 'Treasury',
                'description' => 'Scanned And Validated By Treasury'
            ]);
        } else {
            $steps->push((object) [
                'status' => 'error',
                'title' => 'Treasury',
                'description' => 'Not yet Scanned and Validated By Treasury'
            ]);
        }
        $query =  $promo->join('promo_gc', 'promo_gc.prom_barcode', '=', 'prreltoi_barcode')
            ->leftJoin('promo', 'promo.promo_id', '=', 'promo_gc.prom_promoid')
            ->leftJoin('promogc_released', 'prgcrel_barcode', '=', 'prreltoi_barcode')
            ->where('promo_gc.prom_barcode', $request->barcode)
            ->first();

        // dd($query->promo_name);
        if (empty($query->promo_name)) {
            $steps->push((object) [
                'status' => 'current',
                'title' => 'Marketing',
                'description' => 'Gift Check is Available'
            ]);
        } elseif (!is_null($query->promo_name) && is_null($query->prgcrel_at)) {
            $steps->push((object) [
                'status' => 'wait',
                'title' => 'Marketing',
                'description' => 'Gift Check is Pending'
            ]);
        } else {
            $steps->push((object) [
                'title' => 'Marketing',
                'description' => 'Gift Check is Released'
            ]);
        }
        $q2 =  $promo->join('store_verification', 'store_verification.vs_barcode', '=', 'prreltoi_barcode')->where('store_verification.vs_barcode', $request->barcode);

        if ($promo->whereHas('reverified', fn($query) => $query->where('vs_barcode', $request->barcode))->exists()) {
            $steps->push((object) [
                'title' => 'Verification',
                'description' => 'Verified By CFS at ' . Date::parse($q2->first()->vs_date)->toFormattedDateString(),
            ]);
        } else {
            $steps->push((object) [
                'status' => 'error',
                'title' => 'Verification',
                'description' => 'Not yet Verified By CFS'
            ]);
        }

        // dd($q2->first()->vs_reverifydate);
        // dd($q2->first()->vs_reverifydate);

        $isRevDateExists = $q2->where('vs_barcode', $request->barcode);


        if ($isRevDateExists) {
            $isRev =  $isRevDateExists->whereNotNull('vs_reverifydate')->exists();
        } else {
        }

        $isRevDateNull = $q2->where('vs_barcode', $request->barcode)->where('vs_reverifydate', null)->exists();

        $q2 =  $promo->join('store_verification', 'store_verification.vs_barcode', '=', 'prreltoi_barcode')->where('store_verification.vs_barcode', $request->barcode);

        if ($isRevDateNull) {
            //no result here..
        } elseif ($isRev) {

            $steps->push((object) [
                'title' => 'Reverification',
                'description' => 'Reverified By CFS at ' . Date::parse($q2->first()->vs_reverifydate)->toFormattedDateString(),
            ]);
        } else {
            //no result here..
        }
        $q2 =  $promo->join('store_verification', 'store_verification.vs_barcode', '=', 'prreltoi_barcode')->where('vs_barcode', $request->barcode);

        if ($q2->where('vs_tf_used', '*')->exists()) {
            $steps->push((object) [
                'status' => 'finish',
                'title' => 'Redeemption',
                'description' => 'Redeemed by Customer at ' . $q2->first()->vs_payto,
            ]);
        } else {
            $steps->push((object) [
                'status' => 'error',
                'title' => 'Redeemption',
                'description' => 'Not yet Redeem',
            ]);
        }

        return $steps;
    }

    public static function institutionStatus($step3, $request)
    {

        $steps = collect([
            [
                'title' => 'Treasury',
                'status' => 'finish',
                'description' => 'Request Submitted'
            ],
            [
                'title' => 'Marketing',
                'status' => 'finish',
                'description' => 'Request Approved'
            ],
            [
                'title' => 'FAD',
                'status' => 'finish',
                'description' => 'Scanned By FAD'
            ],
            [
                'title' => 'IAD',
                'status' => 'finish',
                'description' => 'Scanned By IAD'
            ],
        ]);

        $q2 =  $step3->join('store_verification', 'store_verification.vs_barcode', '=', 'instituttritems_barcode')->where('vs_barcode', $request->barcode);

        $vs_date = $q2->first()->vs_date ?? null;
        $rev_date = $q2->first()->vs_reverifydate ?? null;
        $vs_pay = $q2->first()->vs_payto ?? null;


        if ($step3->whereHas('reverified', fn($query) => $query->where('vs_barcode', $request->barcode))->exists()) {

            $steps->push((object) [
                'title' => 'Verification',
                'description' => 'Verified By CFS ' . ' at ' . Date::parse($vs_date)->toFormattedDateString(),
            ]);
        } else {
            $steps->push((object) [
                'status' => 'error',
                'title' => 'Verification',
                'description' => 'Not yet Verified By CFS',

            ]);
        }

        $isRevDateExists = $q2->where('instituttritems_barcode', $request->barcode)->where('vs_reverifydate', $rev_date)->exists();

        $isRevDateNull = $q2->where('instituttritems_barcode', $request->barcode)->where('vs_reverifydate', null)->exists();

        if ($isRevDateNull) {
            //no result here..
        } elseif ($isRevDateExists) {

            $steps->push((object) [
                'title' => 'Reverification',
                'description' => 'Reverified By CFS at ' . Date::parse($rev_date)->toFormattedDateString(),
            ]);
        } else {
            //no result here..
        }


        $q3 =  $step3->join('store_verification', 'store_verification.vs_barcode', '=', 'instituttritems_barcode')->where('vs_barcode', $request->barcode);
        if ($q3->where('vs_tf_used', '*')->exists()) {
            $steps->push((object) [
                'status' => 'finish',
                'title' => 'Redeemption',
                'description' => 'Redeemed by Customer at ' . $vs_pay,
            ]);
        } else {
            $steps->push((object) [
                'status' => 'error',
                'title' => 'Redeemption',
                'description' => 'Not yet Redeem',
            ]);
        }

        return $steps;
    }

    public function supplier()
    {
        return Supplier::all();
    }
    public function stores()
    {
        return Store::select('store_id', 'store_name', 'store_status')
            ->where('store_status', 'active')
            ->get();
    }

    public function getEodDateRange($request)
    {
        $con = is_array($request->date) ? 1 : 2;

        $data =  TransactionStore::join('stores', 'store_id', '=', 'trans_store')
            ->join('store_staff', 'ss_id', '=', 'trans_cashier')
            ->where('trans_yreport', '!=', '0')
            ->where('trans_store', $request->store)
            ->where('trans_eos', '!=', '');

        $data = match ($con) {
            1 => $data->whereBetween('trans_datetime', [$request->date[0], $request->date[1]]),
            2 => $data->whereDate('trans_datetime', $request->date),
        };

        return $data->paginate(10)->withQueryString();
    }

    public function generateReportPdf($request)
    {

        $html = $this->htmlStructure($request);

        $options = new Options();

        $options->set('isHtml5ParserEnabled', true);

        $dompdf = new Dompdf($options);

        $dompdf->loadHtml($html);

        $dompdf->setPaper('letter', 'portrait');

        $dompdf->render();

        $output = $dompdf->output();

        $stream = base64_encode($output);

        $filename = 'Reprint Generated Pdf Report' . '.pdf';

        $filePathName = storage_path('app/' . $filename);


        if (!file_exists(dirname($filePathName))) {
            mkdir(dirname($filePathName), 0755, true);
        }

        Storage::put($filename, $output);

        $filePath = route('download', ['filename' => $filename]);

        return inertia('Admin/Results/PdfResult', [
            'filePath' => $filePath,
            'stream' => $stream,
        ]);
    }


    public function dataReports($request)
    {
        $con = is_array($request->date) ? 1 : 2;

        $data =  TransactionStore::join('stores', 'store_id', '=', 'trans_store')
            ->join('store_staff', 'ss_id', '=', 'trans_cashier')
            ->where('trans_yreport', '!=', '0')
            ->where('trans_store', $request->store)
            ->where('trans_eos', '!=', '');

        $data = match ($con) {
            1 => $data->whereBetween('trans_datetime', [$request->date[0], $request->date[1]]),
            2 => $data->whereDate('trans_datetime', $request->date),
        };

        return $data->get();
    }

    public function htmlStructure($request)
    {
        //<div></div>

        $data = $this->dataReports($request);
        // dd($data->toArray());

        $storename = Store::where('store_id', $request->store)->value('store_name');

        $html = '<html><body style="font-family: "Courier New", Courier, monospace; font-size: 15px;">';
        $html .= '<div style="text-align:center; text-transform: uppercase">' . $storename . '</div>';
        $html .= '<div style="text-align:center; font-size: 10px">Owned & Managed by ASC</div>';
        $html .= '<div style="font-size: 11px; margin-top: 10px;">Date Print: ' . now()->format('M d, Y') . ' </div>';
        $html .= '<hr/>';
        $html .= '<div style="text-align:center; font-weight: 500; letter-spacing: 1px;">GC Cashier Accountability Report</div>';
        $html .= '<hr/>';

        //Cash Side ====>

        $html .= '<div style="font-size: 11px; margin-left: 1px; margin-top: 15px; margin-bottom: 3px; font-weight: bold;">Cash</div>';

        $html .= '<table width="100%" style="border-collapse: collapse; margin-top: 2px">';
        $html .= '<tr>';
        $html .= '<td style="font-size: 11px; width: 50%; text-align: left;">GC Sales</td>';
        $html .= '<td style="font-size: 11px; width: 50%; text-align: center;">0</td>';
        $html .= '<td style="font-size: 11px; width: 50%; text-align: right;">0</td>';
        $html .= '</tr>';
        $html .= '</table>';

        $html .= '<table width="100%" style="border-collapse: collapse; margin-top: 2px">';
        $html .= '<tr>';
        $html .= '<td style="font-size: 11px; width: 50%;  text-align: left;">GC Sales</td>';
        $html .= '<td style="font-size: 11px; width: 50%; text-align: center;">0</td>';
        $html .= '<td style="font-size: 11px; width: 50%;  text-align: right;">0</td>';
        $html .= '</tr>';
        $html .= '</table>';

        $html .= '<table width="100%" style="border-collapse: collapse; margin-top: 2px">';
        $html .= '<tr>';
        $html .= '<td style="font-size: 11px; width: 50%;  text-align: left; font-weight: bold;">Total</td>';
        $html .= '<td style="font-size: 11px; width: 50%;  text-align: center;">0</td>';
        $html .= '<td style="font-size: 11px; width: 50%; text-align: right;">0</td>';
        $html .= '</tr>';
        $html .= '</table>';


        //Cards Side =======>

        $html .= '<div style="font-size: 11px; margin-left: 1px; font-weight: bold; margin-top: 15px">Cards</div>';

        $html .= '<table width="100%" style="border-collapse: collapse; margin-top: 2px">';
        $html .= '<tr>';
        $html .= '<td style="font-size: 11px; width: 50%;  text-align: left;">Total</td>';
        $html .= '<td style="font-size: 11px; width: 50%; text-align: center;">0</td>';
        $html .= '<td style="font-size: 11px; width: 50%;  text-align: right;">0.00</td>';
        $html .= '</tr>';
        $html .= '</table>';

        //Ar Side

        $html .= '<div style="font-size: 11px; margin-left: 1px; font-weight: bold; margin-top: 15px">AR</div>';

        $html .= '<table width="100%" style="border-collapse: collapse; margin-top: 2px">';
        $html .= '<tr>';
        $html .= '<td style="font-size: 11px; width: 50%;  text-align: left;">Total</td>';
        $html .= '<td style="font-size: 11px; width: 50%; text-align: center;">0</td>';
        $html .= '<td style="font-size: 11px; width: 50%;  text-align: right;">0.00</td>';
        $html .= '</tr>';
        $html .= '</table>';

        $html .= '<table width="100%" style="border-collapse: collapse; margin-top: 2px">';
        $html .= '<tr>';
        $html .= '<td style="font-size: 11px; width: 50%;  text-align: left;">GC Refund</td>';
        $html .= '<td style="font-size: 11px; width: 50%; text-align: center;">0</td>';
        $html .= '<td style="font-size: 11px; width: 50%;  text-align: right;">0.00</td>';
        $html .= '</tr>';
        $html .= '</table>';

        $html .= '<table width="100%" style="border-collapse: collapse; margin-top: 2px; margin-bottom: 15px">';
        $html .= '<tr>';
        $html .= '<td style="font-size: 11px; width: 50%;  text-align: left;">Total Refund Charge</td>';
        $html .= '<td style="font-size: 11px; width: 50%; text-align: center;">0</td>';
        $html .= '<td style="font-size: 11px; width: 50%;  text-align: right;">0.00</td>';
        $html .= '</tr>';
        $html .= '</table>';

        //Total

        $html .= '<hr/>';
        $html .= '<table width="100%" style="border-collapse: collapse;">';
        $html .= '<tr>';
        $html .= '<td style="font-size: 11px; width: 50%; font-weight: bold;  text-align: left;">Total</td>';
        $html .= '<td style="font-size: 11px; width: 50%;  text-align: right;">0.00</td>';
        $html .= '</tr>';
        $html .= '</table>';
        $html .= '<hr/>';

        //Discount


        $html .= '<div style="font-size: 11px; margin-left: 1px; margin-top: 15px; font-weight: bold;">Discount</div>';

        $html .= '<table width="100%" style="border-collapse: collapse; margin-top: 2px">';
        $html .= '<tr>';
        $html .= '<td style="font-size: 11px; width: 50%; text-align: left;">Document Discount</td>';
        $html .= '<td style="font-size: 11px; width: 50%; text-align: center;">0</td>';
        $html .= '<td style="font-size: 11px; width: 50%; text-align: right;">0</td>';
        $html .= '</tr>';
        $html .= '</table>';

        $html .= '<table width="100%" style="border-collapse: collapse; margin-top: 2px">';
        $html .= '<tr>';
        $html .= '<td style="font-size: 11px; width: 50%; text-align: left;">Line Discount</td>';
        $html .= '<td style="font-size: 11px; width: 50%; text-align: center;">0</td>';
        $html .= '<td style="font-size: 11px; width: 50%; text-align: right;">0</td>';
        $html .= '</tr>';
        $html .= '</table>';

        $html .= '<table width="100%" style="border-collapse: collapse; margin-top: 2px">';
        $html .= '<tr>';
        $html .= '<td style="font-size: 11px; width: 50%; text-align: left;">Ar Discount</td>';
        $html .= '<td style="font-size: 11px; width: 50%; text-align: center;">0</td>';
        $html .= '<td style="font-size: 11px; width: 50%; text-align: right;">0</td>';
        $html .= '</tr>';
        $html .= '</table>';

        $html .= '<table width="100%" style="border-collapse: collapse; margin-top: 2px">';
        $html .= '<tr>';
        $html .= '<td style="font-size: 11px; width: 50%; text-align: left; font-weight: bold">Total</td>';
        $html .= '<td style="font-size: 11px; width: 50%; text-align: center;">0</td>';
        $html .= '<td style="font-size: 11px; width: 50%; text-align: right;">0</td>';
        $html .= '</tr>';
        $html .= '</table>';

        $html .= '<table width="100%" style="border-collapse: collapse; margin-top: 10px">';
        $html .= '<tr>';
        $html .= '<td style="font-size: 11px; width: 50%; text-align: left;">Total Refund Subtotal Discount</td>';
        $html .= '<td style="font-size: 11px; width: 50%; text-align: right;">0</td>';
        $html .= '</tr>';
        $html .= '</table>';

        $html .= '<table width="100%" style="border-collapse: collapse; margin-top: 2px">';
        $html .= '<tr>';
        $html .= '<td style="font-size: 11px; width: 50%; text-align: left;">Total Refund Line Discount</td>';
        $html .= '<td style="font-size: 11px; width: 50%; text-align: right;">0</td>';
        $html .= '</tr>';
        $html .= '</table>';

        $html .= '<table width="100%" style="border-collapse: collapse; margin-top: 2px">';
        $html .= '<tr>';
        $html .= '<td style="font-size: 11px; width: 50%; text-align: left;">No of Paying Customers</td>';
        $html .= '<td style="font-size: 11px; width: 50%; text-align: right;">0</td>';
        $html .= '</tr>';
        $html .= '</table>';

        $html .= '<table width="100%" style="border-collapse: collapse; margin-top: 2px">';
        $html .= '<tr>';
        $html .= '<td style="font-size: 11px; width: 50%; text-align: left;">No of Transactions </td>';
        $html .= '<td style="font-size: 11px; width: 50%; text-align: right;">0</td>';
        $html .= '</tr>';
        $html .= '</table>';

        $html .= '<table width="100%" style="border-collapse: collapse; margin-top: 2px">';
        $html .= '<tr>';
        $html .= '<td style="font-size: 11px; width: 50%; text-align: left;">Items Sold</td>';
        $html .= '<td style="font-size: 11px; width: 50%; text-align: right;">0</td>';
        $html .= '</tr>';
        $html .= '</table>';

        $html .= '<table width="100%" style="border-collapse: collapse; margin-top: 2px">';
        $html .= '<tr>';
        $html .= '<td style="font-size: 11px; width: 50%; text-align: left;">Total Number of Voided</td>';
        $html .= '<td style="font-size: 11px; width: 50%; text-align: right;">0</td>';
        $html .= '</tr>';
        $html .= '</table>';

        $html .= '<table width="100%" style="border-collapse: collapse; margin-top: 2px">';
        $html .= '<tr>';
        $html .= '<td style="font-size: 11px; width: 50%; text-align: left;">Total Voided Amount </td>';
        $html .= '<td style="font-size: 11px; width: 50%; text-align: right;">0</td>';
        $html .= '</tr>';
        $html .= '</table>';

        $html .= '<table width="100%" style="border-collapse: collapse; margin-top: 2px">';
        $html .= '<tr>';
        $html .= '<td style="font-size: 11px; width: 50%; text-align: left;">Beginning Txnno</td>';
        $html .= '<td style="font-size: 11px; width: 50%; text-align: right;">0</td>';
        $html .= '</tr>';
        $html .= '</table>';

        $html .= '<table width="100%" style="border-collapse: collapse; margin-top: 2px">';
        $html .= '<tr>';
        $html .= '<td style="font-size: 11px; width: 50%; text-align: left;">Ending Txnno</td>';
        $html .= '<td style="font-size: 11px; width: 50%; text-align: right;">0</td>';
        $html .= '</tr>';
        $html .= '</table>';


        $html .= '</body></html>';


        return $html;
    }

    public function getCash()
    {
        // function getTransactionsByModeAndStoreTotalEOS($link, $store, $cashier, $mode)
        // {
        //     $query = $link->query(
        //         "SELECT
        // 		transaction_stores.trans_datetime,
        // 		SUM(transaction_payment.payment_amountdue) as cash,
        // 		COUNT(transaction_stores.trans_sid) as cnt
        // 	FROM
        // 		transaction_stores
        // 	INNER JOIN
        // 		transaction_payment
        // 	ON
        // 		transaction_payment.payment_trans_num = transaction_stores.trans_sid
        // 	WHERE
        // 		transaction_stores.trans_cashier='$cashier'
        // 	AND
        // 		transaction_stores.trans_store='$store'
        // 	AND
        // 		DATE(transaction_stores.trans_datetime) <= CURDATE()
        // 	AND
        // 		transaction_stores.trans_type='$mode'
        // 	AND
        // 		transaction_stores.trans_eos=''
        // "
        //     );
        //     if ($query) {
        //         $row = $query->fetch_object();
        //         return $row;
        //     } else {
        //         return $link->error;
        //     }
        // }
    }


    public function getPoDetailsTextfiles($name)
    {

        $lazy = LazyCollection::make(function () use ($name) {

            $handle = Storage::disk('fad')->readStream('New/' . $name);

            while (($line = fgets($handle)) !== false) {
                yield $line;
            }

            fclose($handle);
        });

        $array = [];

        $denom = [];

        $lazy->each(function ($item, $key) use (&$array, &$denom) {

            $insexp = explode('|', $item);

            $type = self::transactionType($insexp[0]);

            $array[$type] = $insexp[1] ?? null;

            $itemcode = self::denomType($insexp[0]);

            $denom[$itemcode] = $insexp[1] ?? null;
        });

        $recordfiltered = collect($array)->filter(function ($item, $key) {
            return $key !== "";
        });

        $denomfiltered = collect($denom)->filter(function ($item, $key) {
            return $key !== "";
        });

        // dd($denomfiltered);


        return (object) [
            'data' => $recordfiltered,
            'denom' => $denomfiltered,
        ];
    }

    private function transactionType(string $type)
    {
        $transaction = [
            'FAD Purchase Order Details' => 'recno',
            'Receiving No' => 'recno',
            'Transaction Date' => 'transdate',
            'Reference No' => 'refno',
            'Purchase Order No' => 'pon',
            'Purchase Date' => 'purdate',
            'Reference PO No' => 'refpon',
            'Payment Terms' => 'payterms',
            'Location Code' => 'locode',
            'Department Code' => 'depcode',
            'Supplier Name' => 'supname',
            'Mode of Payment' => 'mop',
            'Remarks' => 'remarks',
            'Prepared By' => 'prepby',
            'Checked By' => 'checkby',
            'SRR Type' => 'srrtype',
        ];

        return $transaction[$type] ?? null;
    }

    private static function denomType(string $type)
    {
        $transaction = [
            '00086743' => '00002002',
            '00086744' => '00002003',
        ];

        return $transaction[$type] ?? null;
    }

    public function getDenomination($denom)
    {
        $data = Denomination::select('denomination', 'denom_fad_item_number')->where('denom_status', 'active')->get();

        $data->transform(function ($item) use ($denom) {

            $filtered = collect($denom)->filter(function ($value, $innerkey) use ($item) {

                $denomcode = $item->denom_fad_item_number === $innerkey ?? $value;

                return $denomcode;
            });

            $exploded = explode('.', $filtered[$item->denom_fad_item_number] ?? 0);

            $item->qty = NumberHelper::toLocaleString($exploded[0]) ?? 0;

            return $item;
        });

        return $data;
    }

    public function getpodetailsDatabase()
    {
        $data = RequisitionForm::with('requisFormDenom')->orderByDesc('id')->paginate(10)->withQueryString();

        $data->transform(function ($item) {
            $item->purDate = $item->pur_date->toFormattedDateString();
            $item->transDate = $item->trans_date->toFormattedDateString();
            return $item;
        });

        return $data;
    }

    public function submitOrderPurchase($request)
    {
        // dd($request->name);

        $request->validate([
            'reqno' => 'required|numeric|unique:requisition_form,req_no',
        ]);

        $create = $this->dBTransaction->createPruchaseOrders($request);

        if ($create) {

            // dd(Storage::disk('fad')->exists('New/' . $request->name));
            if (Storage::disk('fad')->exists('New/' . $request->name)) {

                Storage::disk('fad')->move('New/' . $request->name, 'Used/' . $request->name);

                return response()->json([
                    'title' => 'Success',
                    'msg' => 'Successfully Added Po Details',
                    'status' => 'success'
                ]);
            }
        } else {
            return response()->json([
                'title' => 'Error',
                'msg' => 'Failed Added Po Details',
                'status' => 'error'
            ]);
        }
    }

    public function setupPurchaseOrdersDetails() {}
}
