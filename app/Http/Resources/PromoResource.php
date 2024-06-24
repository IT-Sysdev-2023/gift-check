<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PromoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'promo_id' => $this->promo_id,
            'promo_name' => $this->promo_name,
            'promo_date' => $this->promo_date,
            'promo_datenotified' => $this->promo_datenotified,
            'promo_dateexpire' => $this->promo_dateexpire,
            'promo_remarks' => $this->promo_remarks,
            'promo_drawdate' => $this->promo_drawdate,
            'promo_group' => $this->promo_group,
            'user' => $this->whenLoaded('user')
        ];
    }
}
