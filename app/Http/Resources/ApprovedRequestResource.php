<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

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
            'reqap_doc' => $this->when(!is_null($this->reqap_doc), function () {
                $filename = Str::replace('//', '/', $this->reqap_doc);
                return [
                    [
                        'uid' => $this->reqap_id,
                        'url' => "/storage/{$filename}",
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
