<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustodianResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'receiving' => $this->csrr_id,
            'date_received' => $this->csrr_id,
            'e_requisition' => $this->csrr_id,
            'supplier_name' => $this->csrr_id,
            'received_by' => $this->csrr_id,
            'received_type' => $this->csrr_id,
        ];
    }
}
