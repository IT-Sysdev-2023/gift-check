<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Date;

class SpecialGcRequestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */


    public function toArray(Request $request): array
    {

        $test = $this->specialExternalGcrequestItemsHasMany->each(function ($item) {
            return $item->subtotal = (float)$item->specit_denoms * $item->specit_qty;


        });

        return [
            'total' => $test->sum('subtotal'),
            'spcus_companyname' => $this->specialExternalCustomer?->spcus_companyname,
            'spexgc_dateneed' => Date::parse($this->spexgc_dateneed)->toFormattedDateString(),
            'spcus_acctname' => $this->specialExternalCustomer?->spcus_acctname,
            'spexgc_datereq' => Date::parse($this->spexgc_datereq)->toFormattedDateString(),
            'spexgc_reqby' => $this->spexgc_reqby,
            'spexgc_num' => $this->spexgc_num,
            'spexgc_id' => $this->spexgc_id,
            'full_name' => $this->user->full_name,
        ];
    }
}
