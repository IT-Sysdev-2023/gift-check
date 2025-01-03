<?php

namespace App\Http\Resources;

use App\Helpers\NumberHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SpgcLedgerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            'ledger_no' => $this->spgcledger_no,
            'date' => $this->spgcledger_datetime->toFormattedDateString(),
            'transaction' => $this->transactionType($this->spgcledger_type),
            'debit' => NumberHelper::currency($this->spgcledger_debit),
            'credit' => NumberHelper::currency($this->spgcledger_credit),

        ];
    }

    private function transactionType(string $type)
    {
        $transaction = [
            'RFGCSEGC' => 'Special External GC Request(PROMOTIONAL)',
            'RFGCSEGCREL' => 'Special External GC Releasing(PROMOTIONAL)',
        ];

        return $transaction[$type] ?? null;
    }
}
