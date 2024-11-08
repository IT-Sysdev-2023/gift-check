<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AllocationAdjustmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'aadj_id' => $this->aadj_id, 
            'aadj_datetime' => $this->aadj_datetime->toFormattedDateString(), 
            'aadj_type' => $this->aadj_type == 'n' ? 'Negative' : 'Positive', 
            'aadj_remark' => $this->aadj_remark, 
            'aadj_loc' => $this->aadj_loc, 
            'aadj_gctype' => $this->aadj_gctype, 
            'aadj_by'=> $this->aadj_by,
            'store' => $this->whenLoaded('store'),
            'gcType' => $this->whenLoaded('gcType'),
            'user'=> $this->whenLoaded('user'),
        ];
    }
}
