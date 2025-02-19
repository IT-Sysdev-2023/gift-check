<?php

namespace App\Traits;

use App\Models\DtiGcRequest;
use App\Models\DtiGcRequestItem;

trait DtiGcTraits
{
    //
    public function getDtiPendingGcRequest()
    {
        $data = DtiGcRequest::select('dti_num', 'dti_datereq', 'dti_dateneed', 'firstname', 'lastname',)
            ->join('users', 'user_id', 'dti_reqby')
            ->join('special_external_customer', 'spcus_id', 'dti_company')
            ->where('dti_status', 'pending')
            ->where('dti_addemp', 'pending')
            ->get();


        $data->transform(function ($item) {
            $dtiItems = DtiGcRequestItem::where('dti_trid', $item->dti_num)->get();

            $subtotal = 0;

            $dtiItems->each(function ($item) use ($subtotal) {
                
                $subtotal = $item->dti_denoms * $item->dti_qty;

                return $subtotal;
            });


            $item->total += $subtotal;
            return $item;
        });
        dd($data->toArray());
    }
}
