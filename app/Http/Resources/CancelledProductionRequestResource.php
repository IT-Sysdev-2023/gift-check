<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CancelledProductionRequestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'cpr_id' => $this->cpr_id,
            'cpr_by' => $this->cpr_by,
            'cpr_pro_id' => $this->cpr_pro_id,
            'cpr_at' => $this->cpr_at->toDayDateTimeString(),
            'productionRequest' => new ProductionRequestResource($this->whenLoaded('productionRequest')),
            'user' => $this->whenLoaded('user'),
        ];
    }
}
