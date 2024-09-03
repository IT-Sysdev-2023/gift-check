<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GcResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'denom_id' => $this->denom_id, 
            'barcode_no' => $this->barcode_no,
            'denomination' => new DenominationResource($this->whenLoaded('denomination')),
            'custodianSrrItems' => new CustodianSrrItemsResource($this->whenLoaded('custodianSrrItems'))
        ];
    }
}
