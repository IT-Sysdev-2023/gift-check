<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoreGcRequestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'sgc_id' => $this->sgc_id,
            'sgc_num' => $this->sgc_num,
            'sgc_date_needed' => $this->sgc_date_needed,
            'sgc_date_request' => $this->sgc_date_request,
            'sgc_status' => $this->sgc_status == '1' ? 'Partial' : 'Closed',
            'store' => $this->whenLoaded('store'),
            'user' => $this->whenLoaded('user')
           
        ];
    }
}
