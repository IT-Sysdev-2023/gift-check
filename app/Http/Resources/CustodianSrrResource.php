<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustodianSrrResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'rec_no' => sprintf('%03d',  $this->csrr_id),
            'date_rec' => $this->csrr_datetime,
            'e_reqno' => $this->requis_erno,
            'supname' => $this->gcs_companyname,
            'recby' => $this->user->full_name,
            'rectype' => $this->csrr_receivetype,
        ];
    }
}
