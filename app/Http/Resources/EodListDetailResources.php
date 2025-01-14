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

        $dateFormatted = is_null($this->storeverification->vs_reverifydate) ? Date::parse($this->storeverification->vs_date )->toFormattedDateString() .' / ' .
         Date::parse($this->storeverification->vs_time)->format('h:i A') : $this->storeverification->vs_reverifydate;
      return [
        'barcode' => $this->st_eod_barcode,
        'denom' => $this->storeverification->vs_tf_denomination,
        'reverdate' => $dateFormatted,
        'verby' => $this->storeverification->user->full_name,
        'cus' => $this->storeverification->customer->full_name,
        'storename' => $this->storeverification->store->store_name,
        'balance' => $this->storeverification->vs_tf_balance,
      ];
    }
}
