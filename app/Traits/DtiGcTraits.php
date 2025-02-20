<?php

namespace App\Traits;

use App\Models\DtiGcRequest;
use App\Models\DtiGcRequestItem;
use Illuminate\Support\Facades\Date;

trait DtiGcTraits
{
    //
    public function getDtiPendingGcRequest()
    {
        $data = DtiGcRequest::select('dti_num', 'dti_datereq', 'dti_dateneed', 'firstname', 'lastname', 'spcus_companyname',)
            ->join('users', 'user_id', 'dti_reqby')
            ->join('special_external_customer', 'spcus_id', 'dti_company')
            ->where('dti_status', 'pending')
            ->where('dti_addemp', 'pending')
            ->get();


        $data->transform(function ($item) {
            $dtiItems = DtiGcRequestItem::where('dti_trid', $item->dti_num)->get();

            $dtiItems->each(function ($item) {

                $item->subtotal = $item->dti_denoms * $item->dti_qty;

                return $item;
            });

            $item->total += $dtiItems->sum('subtotal');
            $item->dateNeed = Date::parse($item->dti_dateneed)->toFormattedDateString();
            $item->dateReq = Date::parse($item->dti_datereq)->toFormattedDateString();

            return $item;
        });

        return $data;
    }
}
