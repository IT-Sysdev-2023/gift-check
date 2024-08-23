<?php

namespace App\Services\RetailStore;

use App\Models\TempReceivestore;
use Illuminate\Support\Facades\DB;

class RetailDbServices
{
    public function temReceivedStoreCreation($request)
    {
        DB::transaction(function () use ($request) {

            TempReceivestore::create([
                'trec_barcode' => $request->barcode,
                'trec_recnum' => $request->recnum,
                'trec_store' => $request->user()->store_assigned,
                'trec_denid' => $request->denom_id,
                'trec_by' => $request->user()->user_id,
            ]);
        });

        return back()->with([
            'msg' => 'Barcode Scanned Successfully',
            'title' => 'Scanned',
            'status' => 'success',
        ]);
    }
}
