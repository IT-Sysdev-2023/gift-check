<?php

namespace App\Http\Resources;

use App\Helpers\NumberHelper;
use App\Models\ApprovedGcrequest;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BudgetLedgerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            'ledger_no' => $this->bledger_no,
            'date' => $this->bledger_datetime->toFormattedDateString(),
            'transaction' => $this->transactionType($this->bledger_type, $this->approvedGcRequest?->storeGcRequest?->store),
            'debit' => NumberHelper::currency($this->bdebit_amt),
            'credit' => NumberHelper::currency($this->bcredit_amt),

        ];
    }

    private function transactionType(string $type, $store)
    {
        $transaction = [
            'RFBR' => 'Budget Entry',
            'RFGCP' => 'GC',
            'RFGCSEGC' => 'Special External GC Request',
            'RFGCPROM' => 'Promo GC Request',
            'GCPR' => 'Promo GC Releasing',
            'GCSR' => 'GC Releasing'. $store?->store_name,
            'RFGCSEGCREL' => 'Special External GC Releasing',
            'RC' => 'Requisition Cancelled',
            'GCRELINS' => 'Institution GC Releasing',
        ];

        return $transaction[$type] ?? null;
    }
}
