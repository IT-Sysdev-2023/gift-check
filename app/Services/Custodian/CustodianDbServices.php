<?php

namespace App\Services\Custodian;

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
}
