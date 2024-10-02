<?php

namespace App\Http\Resources;

use App\Helpers\NumberHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SpecialExternalGcrequestItemsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            "id"=> $this->specit_id,
            'qty' => $this->specit_qty,
            'denomination' => (float) $this->specit_denoms,
            'primary_id' => $this->specit_trid,

          
            
        ];
    }
}
