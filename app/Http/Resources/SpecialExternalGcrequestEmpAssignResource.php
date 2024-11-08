<?php

namespace App\Http\Resources;

use App\Helpers\NumberHelper;
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
            'denom' => NumberHelper::currency($this->spexgcemp_denom),
            'spexgcemp_extname' => $this->spexgcemp_extname,
            'spexgcemp_fname' => $this->spexgcemp_fname,
            'spexgcemp_lname' => $this->spexgcemp_lname,
            'spexgcemp_mname' => $this->spexgcemp_mname,
        ];
    }
}
