<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SpecialExternalGcrequestEmpAssignResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->spexgcemp_trid,
            'denom' => (float) $this->spexgcemp_denom,
            'qty' => $this->spexgcemp_denom,
        ];
    }
}
