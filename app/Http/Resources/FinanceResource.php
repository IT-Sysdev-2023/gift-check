<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FinanceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'bledger_id' => $this->id,
            'bledger_no' => $this->name,
            'bledger_datetime' => $this->email,
            'bledger_type' => $this->created_at,
            'bdebit_amt' => $this->updated_at,
            'bcredit_amt' => $this->updated_at,
        ];
    }
}
