<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductionRequestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // `production_request`.`pe_id`,
        // 		`production_request`.`pe_num`,
        // 		`production_request`.`pe_date_request`,
        // 		`production_request`.`pe_date_needed`,
        // 		`approved_production_request`.`ape_approved_at`,
        // 		`approved_production_request`.`ape_approved_by`,
        // 		`userequest`.`firstname`,
        // 		`userequest`.`lastname`
        return [
            'pe_id' => $this->pe_id,
            'pe_num' => $this->pe_num,
            'pe_date_request' => $this->pe_date_request->toFormattedDateString(),
            'pe_date_needed' => $this->pe_date_needed->toFormattedDateString(),
            'approvedProductionRequest' => $this->whenLoaded('approvedProductionRequest', function ($data) {
                return [
                    'ape_approved_at' => $data->ape_approved_at->toFormattedDateString(),
                    'ape_approved_by' => $data->ape_approved_by
                ];
            }),
            'user' => $this->whenLoaded('user')
        ];
    }
}
