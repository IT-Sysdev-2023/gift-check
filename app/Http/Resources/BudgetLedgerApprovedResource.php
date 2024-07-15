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
            'date_requested' => $this->br_requested_at,
            'budget_requested' => NumberHelper::currency($this->br_request),
            'prepared_by' => ucfirst($this->firstname) . ' ' . ucfirst($this->lastname),
            'date_approved' => $this->abr_approved_at,
            'approved_by' => $this->abr_approved_by,
           
        ];
    }
}
