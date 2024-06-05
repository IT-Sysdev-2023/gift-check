<?php

namespace App\Http\Resources;

use App\Helpers\NumberHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GcLedgerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            'ledger_no' => $this->cledger_no,
            'date' => $this->cledger_datetime->toFormattedDateString(),
            'transaction' => $this->cledger_desc,
            'debit' => NumberHelper::currency($this->cdebit_amt),
            'credit' => NumberHelper::currency($this->ccredit_amt),
            'posted_by' => $this->user->full_name,
        ];
    }
}
