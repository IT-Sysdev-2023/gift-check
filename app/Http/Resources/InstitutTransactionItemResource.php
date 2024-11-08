<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InstitutTransactionItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "instituttritemsId" => $this->instituttritems_id,
            "instituttritemsBarcode" => $this->instituttritems_barcode,
            "instituttritemsTrid" => $this->instituttritems_trid,
            "gc" => new GcResource($this->whenLoaded('gc')) 
        ];
    }
}
