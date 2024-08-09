<?php

namespace App\Services\Admin;

use App\Models\Denomination;
use App\Models\RequisitionForm;
use App\Models\RequisitionFormDenomination;
use Illuminate\Support\Facades\DB;

class DBTransaction
{
    public function createPruchaseOrders($request, $denomination)
    {

     return DB::transaction(function () use ($denomination, $request) {
            RequisitionForm::create([
                'req_no' => $request->req_no,
                'sup_name' => $request->sup_name,
                'mop' => $request->mop,
                'rec_no' => $request->rec_no,
                'trans_date' => $request->trans_date,
                'ref_no' => $request->ref_no,
                'po_no' => $request->po_no,
                'pay_terms' => $request->pay_terms,
                'loc_code' => $request->loc_code,
                'pur_date' => $request->pur_date,
                'ref_po_no' => $request->ref_po_no,
                'dep_code' => $request->dep_code,
                'remarks' => $request->remarks,
                'prep_by' => $request->prep_by,
                'check_by' => $request->check_by,
                'srr_type' => $request->srr_type,
            ]);

            foreach ($denomination as $key =>  $quantity) {
                $query = Denomination::where('denom_id', $key)->first();

                RequisitionFormDenomination::create([
                    'form_id' => $request->req_no,
                    'denom_no' => $query->denom_fad_item_number,
                    'quantity' => $quantity,
                ]);
            }
        });
    }
}
