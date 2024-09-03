<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustodianSrrItemsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'cssitem_barcode' => $this->cssitem_barcode , 
            'cssitem_recnum' => $this->cssitem_recnum,
            'custodiaSsr' => new CustodianSrrResource($this->whenLoaded('custodiaSsr'))
        ];
    }
}
