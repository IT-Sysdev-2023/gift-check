<?php

namespace App\Services\Custodian;

use App\Http\Resources\CustodianSrrResource;
use App\Models\BarcodeChecker;
use App\Models\CustodianSrr;
use App\Models\Gc;
use Illuminate\Support\Facades\Date;

class CustodianServices
{
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
}
