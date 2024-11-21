<?php

namespace App\Exports\SPGCApprovedExcel;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;


// use Maatwebsite\Excel\Concerns\FromCollection;

class allApprovedExcel implements WithMultipleSheets
{
    /**
    * @return \Illuminate\Support\Collection
    */

    protected $allApprovedData;

    public function __construct($request){
        $this->allApprovedData = $request;
        // dd($this->allApprovedData = $request);
    }
    public function sheets(): array
    {
        return [
            new ExcelExport($this->allApprovedData),
            new perBarcodeExcel($this->allApprovedData)
        ];
    }
}
