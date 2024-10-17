<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductionRequestItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'pe_items_quantity' => $this->pe_items_quantity,
            'denomination' => new DenominationResource($this->whenLoaded('denomination')),
            'barcodeStartEnd' => $this->whenLoaded('barcodeStartEnd'),
        ];
    }
}
