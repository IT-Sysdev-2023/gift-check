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
        return [
            'pe_id' => $this->pe_id,
            'pe_num' => $this->pe_num,
            'pe_date_request' => $this->pe_date_request->toFormattedDateString(),
            'pe_date_request_time' => $this->pe_date_request->format('g:i A'),
            'pe_date_needed' => $this->pe_date_needed->toFormattedDateString(),

            'pe_requested_by' => $this->pe_requested_by,
            'pe_file_docno' => $this->pe_file_docno,
            'pe_remarks' =>$this->pe_remarks,
            'pe_generate_code' => $this->pe_generate_code,
            'pe_requisition' => $this->pe_requisition,
            'pe_type' => $this->pe_type,
            'pe_group' => $this->pe_group,

            'approvedProductionRequest' => new ApprovedProductionRequestResource($this->whenLoaded('approvedProductionRequest')),
            'user' => $this->whenLoaded('user'),
        ];
    }
}
