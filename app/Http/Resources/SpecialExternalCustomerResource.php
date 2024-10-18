<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SpecialExternalCustomerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'spcus_id' => $this->spcus_id, 
            'spcus_companyname' => $this->spcus_companyname, 
            'spcus_acctname' => $this->spcus_acctname,
            'spcus_address' => $this->spcus_address, 
            'spcus_cperson' => $this->spcus_cperson, 
            'spcus_cnumber' => $this->spcus_cnumber, 
            'spcus_at' => $this->spcus_at->toFormattedDateString(),
            'user' => $this->whenLoaded('user'),
        ];
    }
}
