<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InstitutCustomerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'ins_name' => $this->ins_name,
            'ins_custype' => $this->ins_custype,
            'ins_date_created' => $this->ins_date_created->toDayDateTimeString(),
            'user' => $this->whenLoaded('user'),
            'gcType' => $this->whenLoaded('gcType'),

        ];
    }
}
