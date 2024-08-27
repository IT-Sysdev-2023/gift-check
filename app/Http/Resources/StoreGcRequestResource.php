<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Date;

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
            'sgc_date_needed' => $this->sgc_date_needed?->toFormattedDateString(),
            'sgc_remarks' => $this->sgc_remarks,
            'sgc_date_request' => $this->sgc_date_request->toFormattedDateString(),
            'sgc_status' => $this->status($this->sgc_status),
            'store' => $this->whenLoaded('store'),
            'user' => $this->whenLoaded('user'),
            'cancelledStoreGcRequest' => new CancelledStoreGcRequestResource($this->whenLoaded('cancelledStoreGcRequest')),

        ];
    }
    private function status(string $type)
    {
        $transaction = [
            '1' => 'Partial',
            '2' => 'Closed',
        ];

        return $transaction[$type] ?? '';
    }
}
