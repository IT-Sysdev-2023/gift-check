<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentFundResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'pay_id' =>$this->pay_id,
            'pay_desc' => $this->pay_desc,
            'pay_status' => $this->pay_status,
            'pay_dateadded' => $this->pay_dateadded->toFormattedDateString(),
            'user' => $this->whenLoaded('user'),
        ];
    }
}
