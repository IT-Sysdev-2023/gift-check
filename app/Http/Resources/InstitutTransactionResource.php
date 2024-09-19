<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InstitutTransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'institutrTrnum' => $this->institutr_trnum,
            'institucustomer' => $this->whenLoaded('institutCustomer'),
            'date' => $this->institutr_date->toFormattedDateString(),
            'time' => $this->institutr_date->format('H:i:s A'),

        ];
    }
}
