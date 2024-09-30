<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApprovedRequestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'reqap_id' => $this->reqap_id,
            'reqap_trid' => $this->reqap_trid,
            'reqap_preparedby' => $this->reqap_preparedby,
            'reqap_date' => $this->reqap_date?->toFormattedDateString(),
            'reqap_remarks' => $this->reqap_remarks,
            'reqap_doc' => $this->whenNotNull($this->reqap_doc, function () {
                return [
                    [
                        'uid' => $this->reqap_id,
                        'url' => "/storage/{$this->reqap_doc}",
                        'name' => basename($this->reqap_doc),
                    ]
                ];
            })
            ,
            'reqap_checkedby' => $this->reqap_checkedby,
            'reqap_approvedby' => $this->reqap_approvedby,
            'user' => $this->whenLoaded('user'),
            
        ];
    }
}
