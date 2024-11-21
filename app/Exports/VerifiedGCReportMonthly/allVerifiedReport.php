<?php

namespace App\Exports\VerifiedGCReportMonthly;

// use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;


class allVerifiedReport implements WithMultipleSheets
{
    /**
    * @return \Illuminate\Support\Collection
    */

    protected $allVerifiedReportData;

    public function __construct($request){
        $this->allVerifiedReportData= $request;
    }
    public function sheets(): array
    {
        return [
            new verifiedReportMonthly($this->allVerifiedReportData),
        ];
    }
}
