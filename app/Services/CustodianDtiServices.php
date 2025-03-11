<?php

namespace App\Services;

use App\Models\DtiApprovedRequest;
use App\Models\DtiBarcodes;
use App\Models\DtiGcRequest;
use App\Models\User;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Str;

class CustodianDtiServices
{
    public function __construct()
    {
        //
    }

    public function getDtiApprovedRequest()
    {
        $dti = DtiGcRequest::with('customer:spcus_id,spcus_acctname,spcus_companyname')
            ->select(
                'dti_num',
                'dti_company',
                'dti_datereq',
                'dti_dateneed',
                'dti_date',
                'dti_approved_requests.dti_approvedby',
            )
            ->leftJoin('dti_approved_requests', 'dti_approved_requests.dti_trid', '=', 'dti_gc_requests.dti_num')
            ->where('dti_status', 'approved')
            ->where('dti_approvedtype', 'Special External GC Approved')
            ->orderByDesc('dti_num')
            ->paginate(10);

        $dti->each(function ($item) {
            $item->dti_datereq = Date::parse($item->dti_datereq)->toFormattedDateString();
            $item->dti_dateneed = Date::parse($item->dti_dateneed)->toFormattedDateString();
            $item->dti_date = Date::parse($item->dti_date)->toFormattedDateString();
            $item->company = $item->customer->spcus_companyname;
            return $item;
        });

        return $dti;
    }

    public function getDataRequest($id)
    {
        $dti = DtiGcRequest::with([
            'dtiDocuments',
            'user:user_id,firstname,lastname'
        ])
            ->select(
                'dti_num',
                'dti_gc_requests.dti_reqby',
                'dti_datereq',
                'dti_dateneed',
                'dti_gc_requests.dti_remarks',
                'dti_paymenttype',
                'dti_gc_requests.dti_approvedby',
                'dti_empaddby',
                'dti_approved_requests.dti_remarks as appremarks',
                'dti_gc_requests.dti_remarks as remarks',
                'dti_checkby',
                'dti_approved_requests.dti_approvedby',
                'dti_doc',
                'dti_preparedby',
                'dti_approveddate',
            )
            ->join('dti_approved_requests', 'dti_approved_requests.dti_trid', '=', 'dti_gc_requests.dti_num')
            ->where('dti_num', $id)
            ->first();

            if($dti){

                $user =  User::where('user_id', $dti->dti_preparedby)->first();

                $dti->prepby = Str::ucfirst($user->firstname) . ', ' . Str::ucfirst($user->lastname);
            }



        return (object) [
            'records' => $dti,
            'barcodes' => $this->getBarcodesDti($id),
        ];
    }

    private function getBarcodesDti($id){
        $data = DtiBarcodes::select(
            'dti_trid',
            'dti_denom',
            'fname',
            'lname',
            'mname',
            'extname',
            'voucher',
            'address',
            'department',
            'dti_barcode'
        )->where('dti_trid', $id)->paginate(10);

        $data->transform(function ($item) {
            $item->completename = Str::ucfirst($item->fname) . ', ' . Str::ucfirst($item->mname) . ', ' . Str::ucfirst($item->lname) . ' ' . Str::ucfirst($item->extname);
            return $item;
        });

        return $data;
    }
}
