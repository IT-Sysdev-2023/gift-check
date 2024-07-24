<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SpecialExternalGcRequestResource extends JsonResource
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
            'spexgc_payment_arnum' => $this->spexgc_payment_arnum,
            'spexgc_paymentype' => $this->spexgc_paymentype,
            'spexgc_id' => $this->spexgc_id,
            'spexgc_payment' => $this->spexgc_payment,
            'spexgc_datereq' => $this->spexgc_datereq,
            'user' => $this->whenLoaded('user', fn($q) => $q->full_name),
            'specialExternalCustomer' => $this->whenLoaded('specialExternalCustomer'),
            'specialExternalGcrequestItems' => $this->whenLoaded(
                'specialExternalGcrequestItems',
                fn($q) => (float) $q->specit_denoms * (float) $q->specit_qty
            )
        ];
    }
}
