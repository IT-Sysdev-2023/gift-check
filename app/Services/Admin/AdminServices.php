<?php

namespace App\Services\Admin;

use App\Models\Denomination;
use App\Models\RequisitionForm;
use App\Models\RequisitionFormDenomination;
use Illuminate\Support\Facades\Date;

class AdminServices
{
    public function purchaseOrderDetails()
    {
        $collect = RequisitionForm::with('requisFormDenom')->get();

        $collect->transform(function ($item) {
            $item->trans_date = Date::parse($item->trans_date)->toFormattedDateString();
            $item->pur_date = Date::parse($item->pur_date)->toFormattedDateString();
            return $item;
        });

        return $collect;
    }

    public function denomination()
    {
        return Denomination::where('denom_status', 'active')->select('denom_id','denom_fad_item_number', 'denomination')->get();
    }
}
