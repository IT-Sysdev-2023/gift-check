<?php

namespace App\Http\Resources;

use App\Helpers\NumberHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Date;

class BudgetRequestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'br_id' => $this->br_id,
            'br_request' => NumberHelper::currency($this->br_request),
            'br_requested_at' => $this->br_requested_at->toFormattedDateString(),
            'br_requested_at_time' => $this->br_requested_at->format('g:i A'),
            'br_no' => $this->br_no,
            'br_file_docno' => $this->whenNotNull($this->br_file_docno, fn() => "storage/budgetRequestScanCopy/$this->br_file_docno"),
            'br_remarks' => $this->br_remarks,
            'br_requested_needed' => $this->br_requested_needed?->toFormattedDateString(),
            'prepared_by' => $this->user,
            'abr' => new ApprovedBudgetRequestResource($this->whenLoaded('approvedBudgetRequest')),
            'cancelled_request' => $this->whenLoaded('cancelledBudgetRequest'),
            'cancelled_by' => $this->whenLoaded('cancelledBudgetRequest.user')
        ];
    }
}
