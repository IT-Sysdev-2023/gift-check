<?php

namespace App\Services\Treasury;

use App\Models\Gc;
use App\Models\Gcbarcodegenerate;
use App\Models\ProductionRequest;
use App\Models\ProductionRequestItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegularGcProcessService
{
    public function approveProductionRequest(Request $request, $id)
    {
        $q = ProductionRequestItem::select(
            'production_request_items.pe_items_denomination',
            'production_request_items.pe_items_quantity',
            'denomination.denom_barcode_start'
        )
            ->join('denomination', 'denomination.denom_id', '=', 'production_request_items.pe_items_denomination')
            ->where('pe_items_request_id', $id)->get();

        $q->each(function ($item, $key) use ($id, $request) {
            $this->generateGc($request, $id, $item);
        });

        $pr = ProductionRequest::where('pe_id', $id)->update([
            'pe_generate_code' => '1'
        ]);

        if($pr){
            Gcbarcodegenerate::create([
                'gbcg_pro_id' => $id,
                'gbcg_by' => $request->user()->user_id, 
                'gbcg_at' => now()
            ]);
        }

    }

    public function generateGc($request, $id, $item)
    {
        $denom = $item->pe_items_denomination;
        $prefix = $item->denom_barcode_start;
        $qty = $item->pe_items_quantity;

        $petype = ProductionRequest::select('pe_type')->where('pe_id', $id)->first();
        $promo = $petype->pe_type == 2 ? '*' : '';

        $query = Gc::where('denom_id', $denom)->count();

        if ($query < 1) {
            $gcCreate = Gc::create([
                'barcode_no' => $prefix,
                'denom_id' => $denom,
                'date' => today(),
                'time' => now(),
                'pe_entry_gc' => $id,
                'gc_postedby' => $request->user()->user_id,
                'gc_ispromo' => $promo
            ]);
            if ($gcCreate->wasRecentlyCreated) {
                $barcode_no = $prefix;
                $qty = $qty - 1;
            }
        }

        if ($query > 0) {
            $last_bc = Gc::select('barcode_no')->where('denom_id', $denom)->orderByDesc('barcode_no')->first();
            $barcode_no = $last_bc->barcode_no;
        }


        for ($m = 1; $m <= $qty; $m++) {
            $barcode_no++;

            Gc::create([
                'barcode_no' => $barcode_no,
                'denom_id' => $denom,
                'date' => today(),
                'time' => now(),
                'pe_entry_gc' => $id,
                'gc_postedby' => $request->user()->user_id,
                'gc_ispromo' => $promo,
                'status' => ''
            ]);
        }
    }
}