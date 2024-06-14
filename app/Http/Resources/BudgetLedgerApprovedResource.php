<?php

namespace App\Http\Resources;

use App\Helpers\NumberHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Date;

class BudgetLedgerApprovedResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->br_id,
            'br_no' => $this->br_no,
            'date_requested' => Date::parse($this->br_requested_at)->toFormattedDateString(),
            'budget_requested' => NumberHelper::currency($this->br_request),
            'prepared_by' => ucfirst($this->firstname) . ' ' . ucfirst($this->lastname),
            'date_approved' => Date::parse($this->abr_approved_at)->toFormattedDateString(),
            'approved_by' => $this->abr_approved_by,
           
        ];

           //     budget_request.br_request,
    //     budget_request.br_requested_at,
    //     budget_request.br_no,
    //     budget_request.br_file_docno,
    //     budget_request.br_remarks,
    //     budget_request.br_requested_needed,
    }
}
