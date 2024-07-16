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
            'agcr_stat' => $this->agcr_stat,
            'agcr_paymenttype' => $this->agcr_paymenttype,
            'agcr_recby' => $this->agcr_recby,
            'agcr_approved_at' => $this->agcr_approved_at->toFormattedDayDateString(),
            'agcr_approvedby' => $this->agcr_approvedby,
            'agcr_preparedby' => $this->agcr_preparedby,
            'agcr_rec' => $this->agcr_rec,
            'agcr_request_relnum' => $this->agcr_request_relnum,
            'storeGcRequest' => new StoreGcRequestResource($this->whenLoaded('storeGcRequest')),
            // , function ($s) {
            //     return [
            //         'sgc_id' => $s->sgc_id,
            //         'sgc_store' => $s->sgc_store,
            //         'sgc_date_request' => $s->sgc_date_request
            //     ];
            // }
            'user' => $this->whenLoaded('user'),
            'store_name' => $this->whenLoaded('storeGcRequest', fn ($q) => optional($this->storeGcRequest->store)->store_name), 
           
        ];
    }
}
