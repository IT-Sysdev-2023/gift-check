<?php

namespace App\Services\Iad;

use App\Models\CustodianSrr;
use App\Models\CustodianSrrItem;
use App\Models\Denomination;
use App\Models\Gc;
use App\Models\ProductionRequestItem;
use App\Models\RequisitionEntry;
use App\Models\RequisitionForm;
use App\Models\TempValidation;
use Illuminate\Support\Facades\DB;

class IadServices
{
    public function __construct(public IadDbServices $iadDbServices) {}
    public function gcReceivingIndex()
    {
        return RequisitionForm::get();
    }


    public function setupReceivingtxt($request)
    {
        $isEntry = RequisitionEntry::where('requis_erno', $request->requisId)->exists();

        $requisform = RequisitionForm::with('requisFormDenom')->where('req_no', $request->requisId)->first();

        return $isEntry ? $requisform : null;
    }

    public function getRecNum()
    {
        $data =  CustodianSrr::orderByDesc('csrr_id')->first();

        $recnum = !empty($data) ? $data->csrr_id + 1 : 1;

        return $recnum;
    }

    public static function getRequistionNo($requisId)
    {
        return RequisitionEntry::select(
            'requis_erno',
            'requis_id',
            'repuis_pro_id'
        )->where('requis_erno', $requisId)->first()->repuis_pro_id;
    }

    public function getDenomination($denom, $request)
    {


        $requisProId = self::getRequistionNo($request->requisId) ?? null;


        $data =  Denomination::select('denomination', 'denom_fad_item_number', 'denom_code', 'denom_id')
            ->where('denom_type', 'RSGC')
            ->where('denom_status', 'active')
            ->get();

        $cssritem = TempValidation::get()->groupBy('tval_denom');

        $countItems = $cssritem->map(function ($item) {
            return $item->count();
        });


        $data->transform(function ($item) use ($denom, $cssritem, $countItems, $requisProId) {

            $prodRequest = ProductionRequestItem::where('pe_items_request_id', $requisProId)
                ->where('pe_items_denomination', $item->denom_id)->first();
            // dd($prodRequest->toArray());

            foreach ($denom as $key => $value) {
                if ($item->denom_fad_item_number == $value->denom_no) {
                    $item->qty = $value->quantity;
                }
            }
            foreach ($countItems as $key => $value) {

                if ($item->denom_id == $key) {
                    $item->scanned =  $value;
                }
            }

            $ifNotNull = !empty($prodRequest->pe_items_denomination) ? $prodRequest->pe_items_denomination : null;

            if ($item->denom_id ===  $ifNotNull) {
                $item->item_remain = $prodRequest->pe_items_remain ?? null;
            }


            return $item;
        });

        // dd($data->toArray());

        return $data;
    }

    public function validateByRangeServices($request)
    {

        $request->validate([
            'barcodeStart' => 'bail|lt:barcodeEnd|min:13|max:13',
            'barcodeEnd' => 'bail|gt:barcodeStart|min:13|max:13',
        ]);

        $query = RequisitionEntry::where('requis_erno', $request->reqid)->first();

        $inGc =  $query->where('requis_id',   $query->requis_id)
            ->join('gc', 'pe_entry_gc', '=', 'repuis_pro_id')
            ->whereIn('barcode_no', [$request->barcodeStart, $request->barcodeEnd])
            ->get();

        if ($inGc->count() == 2) {
            $isValidated = CustodianSrrItem::where('cssitem_barcode', [$request->barcodeStart, $request->barcodeEnd])->exists();

            if (!$isValidated) {
                $ifNotScanned = TempValidation::whereIn('tval_barcode', [$request->barcodeEnd, $request->barcodeStart])->count() == 2;

                if (!$ifNotScanned) {
                    $denomid = Gc::select('denom_id')->where('barcode_no', $request->barcodeEnd)->first();

                    foreach (range($request->barcodeStart, $request->barcodeEnd) as $barcode) {
                        TempValidation::create([
                            'tval_barcode' => $barcode,
                            'tval_recnum' => $request->recnum,
                            'tval_denom' => $denomid->denom_id,
                        ]);
                    }

                    return back()->with([
                        'status' => 'success',
                        'title' => 'Success',
                        'msg' => 'Barcode # ' . $request->barcodeStart . ' to ' . $request->barcodeEnd . ' is Validated Successfully',
                    ]);
                } else {
                    return back()->with([
                        'status' => 'warning',
                        'title' => 'Info',
                        'msg' => 'Barcode # ' . $request->barcodeStart . ' to ' . $request->barcodeEnd . ' is Already Scanned!',
                    ]);
                }
            } else {
                return back()->with([
                    'status' => 'warning',
                    'title' => 'Info',
                    'msg' => 'Barcode # ' . $request->barcodeStart . ' is Already Validated!',
                ]);
            }
        } else {
            return back()->with([
                'status' => 'error',
                'title' => 'Error!',
                'msg' => 'Barcode ' . $request->barcodeStart . ' to ' . $request->barcodeEnd . ' not Found! ',
            ]);
        }
    }
    public function getScannedGc()
    {
        return TempValidation::select('denom_id', 'tval_denom', 'tval_barcode', 'denomination')
            ->join('denomination', 'denom_id', '=', 'tval_denom')
            ->get();
    }

    public function validateBarcodeFunction($request)
    {
        $request->validate([
            'barcode' => 'bail|min:13|max:13|required',
        ]);

        $query = RequisitionEntry::where('requis_erno', $request->reqid)->first();

        $inGc =  $query->where('requis_id',   $query->requis_id)
            ->join('gc', 'pe_entry_gc', '=', 'repuis_pro_id')
            ->where('barcode_no', $request->barcode)
            ->exists();

        if ($inGc) {
            $ifScanned = TempValidation::where('tval_barcode', $request->barcode)->exists();

            if (!$ifScanned) {
                $denomid = Gc::select('denom_id')->where('barcode_no', $request->barcode)->first();

                TempValidation::create([
                    'tval_barcode' => $request->barcode,
                    'tval_recnum' => $request->recnum,
                    'tval_denom' => $denomid->denom_id,
                ]);

                return back()->with([
                    'status' => 'success',
                    'title' => 'Success',
                    'msg' => 'Barcode # ' . $request->barcode . ' is Validated Successfully',
                ]);
            } else {
                return back()->with([
                    'status' => 'warning',
                    'title' => 'Warning!',
                    'msg' => 'Barcode ' . $request->barcode . ' is Already Scanned! ',
                ]);
            }
        } else {
            return back()->with([
                'status' => 'error',
                'title' => 'Error!',
                'msg' => 'Barcode ' . $request->barcode . ' not Found! ',
            ]);
        }
    }
    public function submitSetupFunction($request)
    {
        DB::transaction(function () use ($request) {

            $id = self::getRequistionNo($request->data['req_no']);

            $this->iadDbServices->custodianPurchaseOrderDetails($request)
                ->custodianSsrCreate($request)
                ->custodianUpProdDetails($request)
                ->custodianSrrItems($request)
                ->custodianDeleteTempValAndReqForm($id, $request->data['req_no']);
        });
    }
}
