<?php

namespace App\Services\Iad;

use App\Models\CustodianSrr;
use App\Models\CustodianSrrItem;
use App\Models\Denomination;
use App\Models\Gc;
use App\Models\RequisitionEntry;
use App\Models\RequisitionForm;
use App\Models\TempValidation;
use Illuminate\Support\Facades\File;

class IadServices
{
    public function gcReceivingIndex()
    {
        return RequisitionForm::get();
    }


    public function setupReceivingtxt($request)
    {
        $isEntry = RequisitionEntry::where('requis_erno', $request->requisId)->exists();

        $requisform = RequisitionForm::with('requisFormDenom')->where('req_no', $request->requisId)->first();

        return $isEntry ? $requisform : [];
    }

    public function getRecNum()
    {
        $data =  CustodianSrr::orderByDesc('csrr_id')->first();

        $recnum = !empty($data) ? $data->csrr_id + 1 : 1;

        return $recnum;
    }

    public function getDenomination($denom)
    {

        $data =  Denomination::select('denomination', 'denom_fad_item_number', 'denom_code','denom_id')
            ->where('denom_type', 'RSGC')
            ->where('denom_status', 'active')
            ->get();

        $cssritem = TempValidation::get()->groupBy('tval_denom');

        $countItems = $cssritem->map(function ($item) {
            return $item->count();
        });


        $data->transform(function ($item) use ($denom, $cssritem, $countItems) {
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
            return $item;
        });

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
}
