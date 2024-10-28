<?php

namespace App\Http\Resources;

use App\Helpers\NumberHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class InstitutTransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'institutrId' => $this->institutr_id,
            'institutrTrnum' => $this->institutr_trnum,
            'institutCustomer' => $this->whenLoaded('institutCustomer'),
            'denomTotal' => $this->whenLoaded('institutTransactionItem', function ($q){
                return  NumberHelper::currency($q->sum(function ($item) {
                    return $item->gc->denomination->denomination;
                }));
            }),
            'institutTransactionItem' => InstitutTransactionItemResource::collection($this->whenLoaded('institutTransactionItem')),
            'document' => new DocumentResource($this->whenLoaded('document')),
            'institutrRemarks' => $this->institutr_remarks,
            'institutrReceivedby' => $this->institutr_receivedby,
            'institutr_paymenttype' => Str::ucfirst($this->institutr_paymenttype),
            'institutTransactionItemCount' => $this->institut_transaction_item_count,
            'date' => $this->institutr_date->toFormattedDateString(),
            'time' => $this->institutr_date->format('H:i:s A'),
        ];
    }
}
