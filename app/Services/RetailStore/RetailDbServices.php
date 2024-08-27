<?php

namespace App\Services\RetailStore;

use App\Helpers\NumberHelper;
use App\Models\ApprovedGcrequest;
use App\Models\LedgerCheck;
use App\Models\LedgerStore;
use App\Models\StoreReceived;
use App\Models\StoreReceivedGc;
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


    public function storeIntoLedgerCheck($request, $data)
    {
        LedgerCheck::create([
            'cledger_no' => $data->cledger_no,
            'cledger_datetime' => today(),
            'cledger_type' => 'SGCR',
            'cledger_desc' => 'Store Gc Received (' . $data->storename . ')',
            'ccredit_amt' => NumberHelper::float($request->total),
            'c_posted_by' => $request->user()->user_id,
        ]);

        return $this;
    }

    public function storeIntoStoreReceived($request, $cldgr)
    {
        StoreReceived::create([
            'srec_recid' => $request->recnum,
            'srec_rel_id' => $request->relnum,
            'srec_store_id' => $request->user()->store_assigned,
            'srec_at' => today(),
            'srec_checkedby' => $request->checkby,
            'srec_by' => $request->user()->user_id,
            'srec_ledgercheckref' => $cldgr,
            'srec_receivingtype' => 'treasury releasing',
        ]);

        return $this;
    }

    public function storeIntoStoreReceivedGc($request, $data)
    {

        foreach ($data->gcs as $key => $value) {

            StoreReceivedGc::create([
                'strec_barcode' => $value->trec_barcode,
                'strec_storeid' => $request->user()->store_assigned,
                'strec_recnum' => $data->recnumid,
                'strec_denom' => $value->trec_denid
            ]);
        }

        return $this;
    }

    public function storeIntoLedgerStore($request, $data)
    {

        LedgerStore::create([
            'sledger_date' => today(),
            'sledger_trans' => 'GCE',
            'sledger_desc' => 'Gift Check Entry',
            'sledger_store' => $request->user()->store_assigned,
            'sledger_debit' => NumberHelper::float($request->total),
            'sledger_no' => $data->sledger_no,
            'sledger_ref' => $data->recnumid,
        ]);

        return $this;
    }

    public function removeTempStore($request)
    {
        TempReceivestore::where('trec_by', $request->user()->user_id)->delete();

        return $this;
    }
    public function updateApprovedGcRequest($request)
    {
        ApprovedGcrequest::where('agcr_request_relnum', $request->relnum)->update(['agcr_rec' => '1']);

        return $this;
    }
}
