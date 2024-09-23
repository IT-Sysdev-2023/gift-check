<?php

namespace App\Http\Resources;

use App\Helpers\NumberHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SpecialExternalGcRequestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        // $denom = $this->specialExternalGcrequestItems()->exists();

        // // dd($denom);
        // if ($denom) {
        //     $record = false;
        // } else {
        //     $record = true;
        // }
        // dd($this->spexgc_paymentype);
        return [
            'spexgc_num' => $this->spexgc_num,
            'spexgc_type' => $this->spexgc_type,
            'spexgc_dateneed' => $this->spexgc_dateneed->toFormattedDateString(),
            'spexgc_payment_arnum' => $this->spexgc_payment_arnum,
            'spexgc_paymentype' => !is_null($this->spexgc_paymentype) ? $this->paymentType($this->spexgc_paymentype) : '',
            'spexgc_id' => $this->spexgc_id,
            'spexgc_payment' => (float) $this->spexgc_payment,
            'spexgc_datereq' => $this->spexgc_datereq->toDayDateTimeString(),
            'spexgc_remarks' => $this->spexgc_remarks,
            'user' => $this->whenLoaded('user', fn($q) => $q->full_name),
            'userAccessPageTitle' => $this->whenLoaded('user', function ($q) {
                return $q->accessPage?->title;
            }),
            'document' => $this->when($this->document->isNotEmpty(), function () {
                return DocumentResource::collection($this->document);
            }),
            'specialExternalCustomer' => $this->whenLoaded('specialExternalCustomer'),
            'specialExternalGcrequestItems' => $this->whenLoaded(
                'specialExternalGcrequestItems',
                fn($q) => (float) $q->specit_denoms * (float) $q->specit_qty
            ),

            'totalGcRequestItems' => $this->whenLoaded('hasManySpecialExternalGcrequestItems', function ($item) {
                $denom = $item->map(function ($i) {
                    return (float) $i->specit_denoms * $i->specit_qty;
                })->sum();

                return NumberHelper::currency($denom);
            }),
            'specialExternalGcrequestEmpAssign' => $this->whenLoaded('specialExternalGcrequestEmpAssign', function ($q) {
                return $q->unique('spexgcemp_denom')->map(function ($item) use ($q) {
                    return [
                        'id' => $item->spexgcemp_trid,
                        'primary_id' => $item->spexgcemp_trid,
                        'denomination' => (float ) $item->spexgcemp_denom,
                        'qty' => $q->count('spexgcemp_trid'),
                    ];
                });
            }),

            'approvedRequest' => $this->whenLoaded('approvedRequest', new ApprovedRequestResource($this->approvedRequest))
        ];
    }
    private function paymentType(int $num)
    {
        $types = [
            1 => 'Cash',
            2 => 'Check',
            3 => 'JV',
            4 => 'AR',
            5 => 'On Account'
        ];

        return $types[$num] ?? '';
    }
}
