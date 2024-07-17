<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApprovedProductionRequestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'ape_approved_at' => $this->ape_approved_at->toFormattedDateString(),
            'ape_approved_by' => $this->ape_approved_by,
            'ape_remarks' => $this->ape_remarks,
            'ape_preparedby' => $this->ape_preparedby,
            'ape_checked_by' => $this->ape_checked_by,
            'user' => $this->whenLoaded('user')
        ];
    }
}
