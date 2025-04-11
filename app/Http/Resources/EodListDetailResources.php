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
        // dd($this->vs_date);
        // dd(is_null($this->storeverification->vs_reverifydate));


        $dateFormatted = is_null($this->vs_reverifydate)
            ? Date::parse($this->vs_date)->toFormattedDateString() . ' / ' .
            Date::parse($this->vs_time)->format('h:i A')
            : $this->vs_reverifydate;

        $fullName = ucfirst($this->firstname) . ' ' . ucfirst($this->lastname);
        $custname = ucfirst($this->cus_fname) . ' ' .
            ucfirst($this->cus_mname) . ' ' .
            ucfirst($this->cus_lname) . ' ' .
            ucfirst($this->cus_namext);
        return [
            'barcode' => $this->st_eod_barcode,
            'denom' => $this->vs_tf_denomination ?? null,
            'reverdate' => $dateFormatted ?? 00,
            'verby' => $fullName ?? null,
            'cus' => $custname ?? null,
            'storename' => $this->store_name ?? null,
            'balance' => $this->vs_tf_balance ?? null,
        ];
    }
}
