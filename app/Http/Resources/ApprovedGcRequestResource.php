<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApprovedGcRequestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'agcr_id' => $this->agcr_id,
            'agcr_approved_at' => $this->agcr_approved_at,
            'agcr_approvedby' => $this->agcr_approvedby,
            'agcr_preparedby' => $this->agcr_preparedby,
            'agcr_rec' => $this->agcr_rec,
            'agcr_request_relnum' => $this->agcr_request_relnum,
            'storeGcRequest' => $this->whenLoaded('storeGcRequest'),
            'user' => $this->whenLoaded('user')
           
        ];
    }
}
