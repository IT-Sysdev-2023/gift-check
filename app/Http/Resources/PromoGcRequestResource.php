<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PromoGcRequestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'req_no' => $this->pgcreq_reqnum,
            'date_req' => $this->pgcreq_datereq,
            'req_id' => $this->pgcreq_id,
            'date_needed' => $this->pgcreq_dateneeded,
            'total' => $this->pgcreq_total,
            'user' => $this->whenLoaded('userReqby', fn ($name)=> $name->fullname),
            'approved_by' => $this->approved_by,
        ];
    }
}

