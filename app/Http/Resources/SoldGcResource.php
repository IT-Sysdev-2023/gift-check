<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SoldGcResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            'barcode' => $this->strec_barcode,
            'denomination' => $this->denomination,
            'gcRequestNo' => $this->strec_recnum,
            'dateSold' => $this->trans_datetime,
            'transactionNo' => $this->trans_number,
            'transactionType' => $this->type($this->trans_type),
            'storeVerified' => $this->store_name,
        ];
    }

    private function type(int $num)
    {
        $types= [
            1 => 'Cash',
            2 => 'Credit Card',
            3 => 'AR Payment'
        ];

        return $types[$num] ?? '';
    }
}
