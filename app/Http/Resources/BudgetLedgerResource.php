<?php

namespace App\Http\Resources;

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
            'ledgerNo' => $this->bledger_no,
            'date' => $this->bledger_datetime->toFormattedDateString(),
            'transaction' => $this->transactionType($this->bledger_type),
            'debit' => $this->bdebit_amt,
            'credit' => $this->bcredit_amt,
        ];
    }

    private function transactionType($type)
    {
        $transaction = [
            'RFBR' => 'Budget Entry',
            'RFGCP' => 'GC',
            'RFGCSEGC' => 'Special External GC Request',
            'RFGCPROM' => 'Promo GC Request',
            'GCPR' => 'Promo GC Releasing',
            'GCSR' => 'GC Releasing',
            'RFGCSEGCREL' => 'Special External GC Releasing',
            'RC' => 'Requisition Cancelled',
            'GCRELINS' => 'Institution GC Releasing',
        ];

        return $transaction[$type] ?? null;
            // if ($type == 'RFBR') {
            //     return 'Budget Entry';

            // } elseif ($type == 'RFGCP') {
            //     return 'GC';
            // } elseif ($type == 'RFGCSEGC') {
            //     return 'Special External GC Request';

            // } elseif ($type == 'RFGCPROM') {
            //     return 'Promo GC Request';
            // } elseif ($type == 'RFGCPROM') {
            //     return 'Promo GC Releasing';
            //        } elseif ($item->bledger_type == 'GCSR') {

            //     $data = ApprovedGcrequest::join('store_gcrequest', 'store_gcrequest.sgc_id', '=', 'approved_gcrequest.agcr_request_id')
            //         ->join('stores', 'stores.store_id', '=', 'store_gcrequest.sgc_store')
            //         ->where('approved_gcrequest.agcr_id', $item->bledger_trid)
            //         ->select('stores.store_name')
            //         ->first();

            //     $item->transactionType = 'GC Releasing - ' . $data->store_name;
            // } elseif ($type == 'RFGCSEGCREL') {
            //     return 'Special External GC Releasing';
            // } elseif ($type == 'RC') {
            //     return 'Requisition Cancelled';
            // } elseif ($type == 'GCRELINS') {
            //     return 'Institution GC Releasing';
            // }
    }
}
