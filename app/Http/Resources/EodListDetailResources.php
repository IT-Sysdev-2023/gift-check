<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Date;

class EodListDetailResources extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // dd($this->store_eod_barcode);
        // dd(is_null($this->storeverification->vs_reverifydate));

        if ($this->storeverification) {
            $dateFormatted = is_null($this->storeverification->vs_reverifydate)
                ? Date::parse($this->storeverification->vs_date)->toFormattedDateString() . ' / ' .
                Date::parse($this->storeverification->vs_time)->format('h:i A')
                : $this->storeverification->vs_reverifydate;
        } else {
            $dateFormatted = 'N/A'; // or any fallback value
        }
        return [
            'barcode' => $this->st_eod_barcode,
            'denom' => $this->storeverification->vs_tf_denomination ?? null,
            'reverdate' => $dateFormatted ?? 00,
            'verby' => $this->storeverification->user->full_name ?? null,
            'cus' => $this->storeverification->customer->full_name ?? null,
            'storename' => $this->storeverification->store->store_name ?? null,
            'balance' => $this->storeverification->vs_tf_balance ?? null,
        ];
    }
}
