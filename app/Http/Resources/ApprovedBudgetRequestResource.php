<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApprovedBudgetRequestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            'id' => $this->id,
            'approved_by' => $this->abr_approved_by,
            'approved_at' => $this->abr_approved_at->toFormattedDateString(),
            'file_doc_no' => $this->abr_file_doc_no,
            'checked_by' => $this->abr_checked_by,
            'budget_remark' => $this->approved_budget_remark,
            'user_prepared_by' => $this->user
        ];
    }
}
