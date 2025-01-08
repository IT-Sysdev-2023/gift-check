<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
      return [
        'barcode' => $this->st_eod_barcode,
        'denom' => $this->storeverification->vs_tf_denomination,
        'reverdate' => is_null($this->storeverification->vs_reverifydate) ? $this->storeverification->vs_date .' / '  . $this->storeverification->vs_time : $this->storeverification->vs_reverifydate,
        'verby' => $this->storeverification->user->full_name,
        'cus' => $this->storeverification->customer->full_name,
        'storename' => $this->storeverification->store->store_name,
        'balance' => $this->storeverification->vs_tf_balance,
      ];
    }
}
