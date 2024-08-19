<?php

namespace App\Services\Custodian;

use App\Helpers\NumberInWordsHelper;
use App\Http\Resources\CustodianSrrResource;
use App\Http\Resources\SpecialGcRequestResource;
use App\Models\BarcodeChecker;
use App\Models\CustodianSrr;
use App\Models\Document;
use App\Models\Gc;
use App\Models\SpecialExternalGcrequest;
use App\Models\SpecialExternalGcrequestEmpAssign;
use Illuminate\Support\Facades\Date;
use App\Services\Custodian\CustodianDbServices;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Number;
use Milon\Barcode\DNS1D;

class CustodianServices
{
    public function __construct(public CustodianDbServices $custodianDbServices) {}
    public function barcodeChecker()
    {
        $data = BarcodeChecker::with(
            'users:user_id,firstname,lastname',
            'gc:barcode_no,denom_id',
            'gc.denomination:denom_id,denomination'
        )
            ->orderByDesc('bcheck_date')
            ->limit(10)->get();

        $data->transform(function ($item) {
            $item->fullname = $item->users->full_name;
            $item->bcheck_date = Date::parse($item->bcheck_date)->toFormattedDateString();
            $item->denomination = $item->gc->denomination->denomination ?? null;
            return $item;
        });

        return $data;
    }
    public function searchBarcode($request)
    {
        $data = BarcodeChecker::with(
            'users:user_id,firstname,lastname',
            'gc:barcode_no,denom_id',
            'gc.denomination:denom_id,denomination'
        )->where('bcheck_barcode', $request->search)
            ->orderByDesc('bcheck_date')
            ->limit(1)->get();

        $data->transform(function ($item) {
            $item->bcheck_date = Date::parse($item->bcheck_date)->toFormattedDateString();
            return $item;
        });
        return $data;
    }
    public function reqularGcScannedCount()
    {
        return BarcodeChecker::whereHas('gc', function ($query) {
            $query->whereColumn('barcode_no', 'bcheck_barcode');
        })->count();
    }

    public function specialExternalGcCount()
    {
        return BarcodeChecker::whereHas('special', function ($query) {
            $query->whereColumn('spexgcemp_barcode', 'bcheck_barcode');
        })->count();
    }

    public function totalGcCount()
    {
        return BarcodeChecker::with('gc')->count();
    }

    public function todaysCount()
    {
        return BarcodeChecker::whereDate('bcheck_date', today())->count();
    }

    public function scannedBarcodeFn($request)
    {
        $isInGc = Gc::where('barcode_no', $request->barcode)->exists();

        $isInBc = BarcodeChecker::where('bcheck_barcode', $request->barcode)->exists();

        $scanby = BarcodeChecker::with('scannedBy:user_id,firstname,lastname')
            ->where('bcheck_barcode', $request->barcode)
            ->first();

        if ($isInGc && !$isInBc) {

            BarcodeChecker::create([
                'bcheck_barcode' => $request->barcode,
                'bcheck_checkby' => $request->user()->user_id,
                'bcheck_date' => now(),
            ]);

            return response()->json([
                'msg' => 'Scan Successfully',
                'status' => 'success',
                'desc' => 'The Barcode ' . $request->barcode . ' Scanned Successfully',
            ]);
        } elseif ($isInBc) {
            return response()->json([
                'msg' => 'Already Scanned',
                'status' => 'warning',
                'desc' => 'The Barcode ' . $request->barcode . ' is Already Scanned By ' . $scanby->scannedBy->full_name,
            ]);
        } else {
            return response()->json([
                'msg' => '404 not Found!',
                'status' => 'error',
                'desc' => 'Oppss! The Barcode ' . $request->barcode . ' not found',
            ]);
        }
    }

    public function receivedgcIndex()
    {

        $collection = CustodianSrr::with('user:user_id,firstname,lastname')
            ->select('csrr_id', 'csrr_datetime', 'requis_erno', 'gcs_companyname', 'csrr_receivetype', 'csrr_prepared_by')
            ->join('requisition_entry', 'requis_id', '=', 'csrr_requisition')
            ->join('supplier', 'gcs_id', '=', 'requis_supplierid')
            ->orderByDesc('csrr_id')
            ->paginate(10)
            ->withQueryString();

        return CustodianSrrResource::collection($collection);
    }

    public function specialExternalGcEntry($request)
    {
        $key = $request->activeKey == '2' ? '*' : '0';

        $data =  SpecialExternalGcrequest::selectFilterEntry()
            ->with('user:user_id,firstname,lastname', 'specialExternalCustomer:spcus_id,spcus_acctname,spcus_companyname', 'specialExternalGcrequestItemsHasMany:specit_trid,specit_denoms,specit_qty')
            ->where('spexgc_status', 'pending')
            ->where('spexgc_addemp', 'pending')
            ->where('spexgc_promo', $key)
            ->orderByDesc('spexgc_num')
            ->get();

        return SpecialGcRequestResource::collection($data);
    }

    public function specialExternalGcSetup($request)
    {

        $data = SpecialExternalGcrequest::selectFilterSetup()
            ->with('user:user_id,firstname,lastname', 'specialExternalCustomer:spcus_id,spcus_acctname,spcus_companyname', 'specialExternalGcrequestItemsHasMany:specit_trid,specit_denoms,specit_qty')
            ->where('spexgc_id', $request->id)
            ->where('spexgc_status', 'pending')
            ->get();



        $count = 1;
        $data->transform(function ($item) use (&$count) {

            $item->specialExternalGcrequestItemsHasMany->each(function ($subitem) use (&$count) {
                $subitem->tempId = $count++;
                $subitem->subtotal = $subitem->specit_denoms * $subitem->specit_qty;
                return $subitem;
            });

            $item->total =  $item->specialExternalGcrequestItemsHasMany->sum('subtotal');

            return $item;
        });

        return $data;
    }

    public function submitSpecialExternalGc($request)
    {
        DB::transaction(function () use ($request) {
            $this->custodianDbServices
                ->specialGcExternalEmpAssign($request)
                ->updateSpecialExtRequest($request->reqid);
        });

        return redirect()->route('custodian.dashboard')->with([
            'status' => 'success',
            'msg' => 'Successfully Submitted Form',
            'title' => 'Success',
        ]);
    }

    public function approvedGcList()
    {
        $data = SpecialExternalGcrequest::with('specialExternalCustomer:spcus_id,spcus_acctname,spcus_companyname')
            ->selectFilterApproved()
            ->leftJoin('approved_request', 'reqap_trid', '=', 'spexgc_id')
            ->where('spexgc_status', 'approved')
            ->where('reqap_approvedtype', 'Special External GC Approved')
            ->orderByDesc('spexgc_num')
            ->paginate(10)
            ->withQueryString();

        $data->transform(function ($item) {

            $item->company = $item->specialExternalCustomer->spcus_companyname;

            $item->spexgc_datereq = Date::parse($item->spexgc_datereq)->toFormattedDateString();
            $item->spexgc_dateneed = Date::parse($item->spexgc_dateneed)->toFormattedDateString();
            $item->reqap_date = Date::parse($item->reqap_date)->toFormattedDateString();

            return $item;
        });

        return $data;
    }

    public function setupApprovalSelected($request)
    {

        $docs =  Document::where('doc_trid', $request->id)
            ->where('doc_type', 'Special External GC Request')
            ->first();

        $special = SpecialExternalGcrequest::with('user:user_id,firstname,lastname', 'specialExternalCustomer', 'approvedRequest.user')
            ->selectFilterSetupApproval()
            ->withWhereHas('approvedRequest', function ($query) {
                $query->where('reqap_approvedtype', 'Special External GC Approved');
            })
            ->where('spexgc_status', 'approved')
            ->where('spexgc_id', $request->id)
            ->first();

        if ($special) {
            if ($special->spexgc_paymentype == '1') {
                $special->paymentStatus = 'Cash';
            } elseif ($special->spexgc_paymentype == '2') {
                $special->paymentStatus = 'Check';
            } elseif ($special->spexgc_paymentype == '3') {
                $special->paymentStatus = 'JV';
            } elseif ($special->spexgc_paymentype == '4') {
                $special->paymentStatus = 'AR';
            } elseif ($special->spexgc_paymentype == '5') {
                $special->paymentStatus = 'On Account';
            }
        }


        return (object) [
            'docs' => $docs,
            'special' => $special,
        ];
    }

    public function setupApprovalBarcodes($request)
    {

        $data = SpecialExternalGcrequestEmpAssign::select(
            'spexgcemp_trid',
            'spexgcemp_denom',
            'spexgcemp_fname',
            'spexgcemp_lname',
            'spexgcemp_mname',
            'spexgcemp_extname',
            'voucher',
            'address',
            'department',
            'spexgcemp_barcode'
        )->where('spexgcemp_trid', $request->id)->get();

        $data->transform(function ($item) {
            $item->completename = $item->spexgcemp_fname . ' ' . $item->spexgcemp_mname . ' ' .  $item->spexgcemp_lname . ' ' . $item->spexgcemp_extname;
            return $item;
        });

        return $data;
    }

    public function getSpecialExternalGcRequest($request)
    {
        $data = SpecialExternalGcrequestEmpAssign::with(
            'specialExternalGcRequest:spexgc_id,spexgc_company',
            'specialExternalGcRequest.specialExternalCustomer:spcus_id,spcus_acctname'
        );

        $data = match ($request->status) {
            '1' =>  $data->where('spexgcemp_barcode', $request->barcode)->get(),
            '2' =>  $data->whereBetween('spexgcemp_barcode', [$request->barcodeStart, $request->barcodeEnd])->get(),
        };


        $data->transform(function ($item) {

            $barcode = new DNS1D();

            $html = $barcode->getBarcodePNG($item->spexgcemp_barcode, 'C128');

            $item->barcode = $html;
            $item->numWords = Number::spell($item->spexgcemp_denom) . ' pesos only';

            return $item;
        });

        return $data;
    }
}
