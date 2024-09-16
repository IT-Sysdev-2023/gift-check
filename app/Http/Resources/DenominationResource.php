<?php

namespace App\Http\Resources;

use App\Helpers\NumberHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DenominationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->denom_id,
            'denomination' => $this->denomination,
            'denomination_format' => $this->denomination_format,

            'qty' => $this->whenLoaded('productionRequestItems', fn($q) => $q->pe_items_quantity)
        ];
    }
}
