<?php

namespace App\Services;

use App\Models\DtiGcRequest;

class CustodianDtiServices
{
    public function __construct()
    {
        //
    }

    public function getDtiApprovedRequest(){
        // $dti = DtiGcRequest::with('customer:spcus_id,spcus_acctname,spcus_companyname')
        // ->leftJoin('approved_request', 'reqap_trid', '=', 'spexgc_id')
        // ->get();

        // dd($dti->toArray());
    }
}
