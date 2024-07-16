<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Date;

class CancelledStoreGcRequestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'csgr_id' => $this->csgr_id,
            'csgr_gc_id' => $this->csgr_gc_id,
            'csgr_by' => $this->csgr_by,
            'csgr_at' => $this->csgr_at->toFormattedDateString(),
            'user' => $this->whenLoaded('user')
        ];
    }
}
