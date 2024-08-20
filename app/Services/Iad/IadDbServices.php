<?php

namespace App\Services\Iad;

use App\Models\CustodianSrr;
use App\Models\CustodianSrrItem;
use App\Models\Gc;
use App\Models\ProductionRequestItem;
use App\Models\PurchaseOrderDetail;
use App\Models\RequisitionEntry;
use App\Models\RequisitionForm;
use App\Models\RequisitionFormDenomination;
use App\Models\TempValidation;

class IadDbServices
{
    public function custodianSsrCreate($request)
    {
        CustodianSrr::create([
            'csrr_id' => $request->recnum,
            'csrr_requisition' => $request->data['req_no'],
            'csrr_receivetype' => $request->data['srr_type'],
            'csrr_datetime' => today(),
            'csrr_prepared_by' => $request->user()->user_id,
            'csrr_receivedas' => $request->select,
            'csrr_remarks' => '',
        ]);
        return $this;
    }
    public function custodianPurchaseOrderDetails($request)
    {
        PurchaseOrderDetail::create([
            'purchorderdet_mnlno' => $request->data['req_no'],
            'purchorderdet_fadrecno' => $request->data['rec_no'],
            'purchorderdet_trandate' => $request->data['trans_date'],
            'purchorderdet_refno' => $request->data['ref_no'],
            'purchorderdet_purono' => $request->data['po_no'],
            'purchorderdet_purdate' => $request->data['pur_date'],
            'purchorderdet_payterms' => $request->data['pay_terms'],
            'purchorderdet_locode' => $request->data['loc_code'],
            'purchorderdet_deptcode' => $request->data['dep_code'],
            'purchorderdet_supname' => $request->data['sup_name'],
            'purchorderdet_modpay' => $request->data['mop'],
            'purchorderdet_remarks' => $request->data['remarks'],
            'purchorderdet_prepby' => $request->data['prep_by'],
            'purchorderdet_checkby' => $request->data['check_by']
        ]);
        return $this;
    }
    public function custodianSrrItems($request)
    {

        foreach ($request->scanned as $barcode) {
            CustodianSrrItem::create([
                'cssitem_barcode' => $barcode['tval_barcode'],
                'cssitem_recnum' => $request->recnum
            ]);
        }
        return $this;
    }

    public function custodianUpProdDetails($request)
    {


        $reqNo = self::getRequistionNo($request->data);

        foreach ($request->denom as $denom) {
            $prodRequest = ProductionRequestItem::where('pe_items_request_id', $reqNo)
                ->where('pe_items_denomination', $denom['denom_id']);

            $ifDenom = $prodRequest->first()->pe_items_denomination ?? null;

            $ifRemainItem = $prodRequest->first()->pe_items_remain ?? null;

            if ($ifDenom === $denom['denom_id']) {
                $ifRemainItem -= $denom['scanned'] ?? null;
            }

            $prodRequest->update([
                'pe_items_remain' => $ifRemainItem
            ]);


        }

        // dd($prodRequest);

        return $this;
    }
    public function custodianUsedAndValidated($id, $recnum, $type)
    {
        if (($type == 'whole' || $type == 'final')) {

            RequisitionForm::where('req_no', $recnum)->update([
                'used' => 'used',
            ]);

            RequisitionFormDenomination::where('form_id', $recnum)->update([
                'used' => 'used'
            ]);
        }


        return $this;
    }

    public function custodianGcUpdate($request)
    {

        foreach($request->scanned as $barcode){
            Gc::where('barcode_no', $barcode['tval_barcode'])->update([
                'gc_validated' => '*'
            ]);
        }
        return $this;
    }
    public function custodianRequisitionUpdate($request)
    {
        $rectype = strtoupper($request->select);

        if ($rectype == 'PARTIAL') {
            $recStatus = 1;
        } elseif ($rectype == 'WHOLE') {
            $recStatus = 2;
        } elseif ($rectype == 'FINAL') {
            $recStatus = 2;
        }
        RequisitionEntry::where('requis_erno', $request->data['req_no'])->update([
            'requis_status' => $recStatus
        ]);
        return $this;
    }


    public static function getRequistionNo($data)
    {
        return RequisitionEntry::select(
            'requis_erno',
            'requis_id',
            'repuis_pro_id'
        )->where('requis_erno', $data['req_no'])->first()->repuis_pro_id;
    }
}
