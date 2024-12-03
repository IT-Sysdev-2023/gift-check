<?php

namespace App\Services\Custodian;

use App\Models\ApprovedRequest;
use App\Models\Document;
use App\Models\SpecialExternalGcrequest;
use App\Models\SpecialExternalGcrequestEmpAssign;

class CustodianDbServices
{
    public function specialGcExternalEmpAssign($request)
    {
        foreach ($request->data as $item) {
            SpecialExternalGcrequestEmpAssign::create([
                'spexgcemp_extname' => $item['suffix'],
                'spexgcemp_barcode' => 0,
                'spexgcemp_review' => '',
                'spexgc_status' => '',
                'spexgcemp_denom' => $item['denom'],
                'spexgcemp_fname' => $item['firstname'],
                'spexgcemp_lname' => $item['lastname'],
                'spexgcemp_mname' => $item['middlename'],
                'spexgcemp_trid' => $item['reqid'],
                'payment_id' => 0,
                'department' => '',
                'address' => $item['address'],
                'voucher' => $item['voucher'],
                'bunit' => $item['business'],
            ]);
        }

        return $this;
    }

    public function updateSpecialExtRequest($reqid)
    {
        SpecialExternalGcrequest::where('spexgc_id', $reqid)
            ->where('spexgc_status', 'pending')
            ->where('spexgc_addemp', 'pending')
            ->update([
                'spexgc_addempaddby' => request()->user()->user_id,
                'spexgc_addempdate' => now(),
                'spexgc_addemp' => 'done',
            ]);

        return $this;
    }

    public function getSpecialExternalGcRequest($id)
    {
        return SpecialExternalGcrequest::select(
            'spexgc_reqby',
            'spexgc_company',
            'spexgc_id',
            'spexgc_num',
            'spexgc_datereq',
            'spexgc_dateneed',
            'spexgc_remarks',
            'spexgc_payment',
            'spexgc_paymentype',
            'spexgc_receviedby',
            'spexgc_id',
            'firstname',
            'lastname',
            'reqap_date',
            'reqap_remarks',
            'reqap_doc',
            'reqap_checkedby',
            'reqap_approvedby',
            'reqap_preparedby',
            'reqap_date',
        )
            ->join('approved_request', 'reqap_trid', 'spexgc_id')
            ->join('users', 'user_id', 'reqap_preparedby')
            ->with(
                'specialExternalBankPaymentInfo:spexgcbi_id,spexgcbi_bankname,spexgcbi_bankaccountnum,spexgcbi_checknumber',
                'specialExternalCustomer:spcus_id,spcus_acctname,spcus_companyname',
                'user:user_id,firstname,lastname'
            )
            ->where('spexgc_status', 'approved')
            ->where('reqap_approvedtype', 'Special External GC Approved')
            ->where('spexgc_id', $id)
            ->first();
    }

    public function getDocs($id)
    {
        return Document::select('doc_fullpath')->where('doc_trid', $id)
            ->where('doc_type', 'Special External GC Request')->get();
    }

    public function getApprovedRequest($id)
    {
        return ApprovedRequest::select('reqap_remarks', 'reqap_date', 'reqap_preparedby')
            ->with('user:user_id,firstname,lastname')
            ->where('reqap_trid', $id)
            ->where('reqap_approvedtype', 'special external gc review')
            ->first();
    }
    public function getReleasedRequest($id)
    {
        return ApprovedRequest::select('reqap_remarks', 'reqap_date', 'reqap_preparedby')
            ->with('user:user_id,firstname,lastname')->where('reqap_trid', $id)
            ->where('reqap_approvedtype', 'special external releasing')
            ->first();
    }

}
