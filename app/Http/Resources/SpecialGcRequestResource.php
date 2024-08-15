<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SpecialGcRequestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */


    public function toArray(Request $request): array
    {

        return [
            'spexgc_num' => $this->spexgc_num,
            'spexgc_dateneed' => $this->spexgc_dateneed,
            'spexgc_reqby' => $this->spexgc_reqby,
            'spexgc_datereq' => $this->spexgc_datereq,
            'full_name' => $this->user->full_name,
            'spcus_acctname' => $this->specialExternalCustomer?->spcus_acctname,
            'spcus_companyname' => $this->specialExternalCustomer?->spcus_companyname,
            'items_calculation' => (float)($this->specialExternalGcrequestItems->specit_denoms) * (float)($this->specialExternalGcrequestItems->specit_qty),
            'denoms' => $this->specialExternalGcrequestItems->specit_denoms,
            'qty' => $this->specialExternalGcrequestItems->specit_qty,
        ];
    }
}
