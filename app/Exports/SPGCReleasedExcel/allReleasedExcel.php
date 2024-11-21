<?php

namespace App\Exports\SPGCReleasedExcel;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;


// use Maatwebsite\Excel\Concerns\FromCollection;

class allReleasedExcel implements WithMultipleSheets
{
    /**
    * @return \Illuminate\Support\Collection
    */

    protected $allReleaseData;

    public function __construct($request){
        $this->allReleaseData = $request;
    }
    public function sheets(): array
    {
        return [
            new releasePerBarcode($this->allReleaseData),
            new releasePerCustomer($this->allReleaseData)
        ];
    }
}
