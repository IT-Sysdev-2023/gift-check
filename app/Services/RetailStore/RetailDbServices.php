<?php

namespace App\Services\RetailStore;

use App\Helpers\NumberHelper;
use App\Models\ApprovedGcrequest;
use App\Models\LedgerCheck;
use App\Models\LedgerStore;
use App\Models\Store;
use App\Models\StoreReceived;
use App\Models\StoreReceivedGc;
use App\Models\StoreVerification;
use App\Models\TempReceivestore;
use App\Models\TransactionRevalidation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

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
        // dd($request->all());
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

    public function storeInStoreVerification($request, $data)
    {
        return StoreVerification::create([
            'vs_barcode' => $request->barcode,
            'vs_cn' => $request->customer,
            'vs_by' => $request->user()->user_id,
            'vs_date' => today(),
            'vs_time' => now()->format('H:i:s'),
            'vs_tf' => $request->barcode . $data['tfilext'],
            'vs_store' => $request->user()->store_assigned,
            'vs_tf_balance' => $data['denom'],
            'vs_gctype' => $data['gctype'],
            'vs_tf_denomination' => $data['denom'],
            'vs_payto' => $request->payment,
        ]);
    }

    public function updateRevalidation($request)
    {
        StoreVerification::where('vs_barcode', $request->barcode)->update([
            'vs_reverifydate' => now(),
            'vs_reverifyby' => $request->user()->user_id,
            'vs_tf_eod' => ''
        ]);
    }

    public function updateRevalidationTransaction($request)
    {

        TransactionRevalidation::where('reval_barcode', $request->barcode)->update([
            'reval_revalidated' => '1',
        ]);
    }
    public function createtextfile($request, $data)
    {
        try {
            $st = Store::where('store_id', $request->user()->store_assigned)->first();

            $networkPath = rtrim(str_replace('\\', '\\\\', $st->store_textfile_ip), '\\') . '\\';
            $filePath = $networkPath . $request->barcode . $data['tfilext'];

            $content = "000," . $request->customer . ",0," . $data['customer']->full_name . "\n";
            $content .= "001," . $data['denom'] . "\n";
            $content .= "002,0\n";
            $content .= "003,0\n";
            $content .= "004," . $data['denom'] . "\n";
            $content .= "005,0\n";
            $content .= "006,0\n";
            $content .= "007,0\n";

            $success = file_put_contents($filePath, $content);

            if ($success === false) {
                return false;
            }

            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }
    public function createtextfileSecondaryPath($request, $data)
    {
        $filePath = '\\\172.16.43.166\\Gift\\' . $request->barcode . $data['tfilext'];

        $content = "000," . $request->customer . ",0," . $data['customer']->full_name . "\n";
        $content .= "001," . $data['denom'] . "\n";
        $content .= "002,0\n";
        $content .= "003,0\n";
        $content .= "004," . $data['denom'] . "\n";
        $content .= "005,0\n";
        $content .= "006,0\n";
        $content .= "007,0\n";

        file_put_contents($filePath, $content);
    }
}
