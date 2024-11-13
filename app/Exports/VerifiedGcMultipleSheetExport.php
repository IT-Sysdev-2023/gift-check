<?php

namespace App\Exports;

use App\Exports\IadVerifiedExports\PerGcTypeAndBuExports;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\IadVerifiedExports\VerifiedPerDayExport;
use App\Exports\IadVerifiedExports\VerifiedSummaryExports;

class VerifiedGcMultipleSheetExport implements WithMultipleSheets
{
    /**
    * @return \Illuminate\Support\Collection
    */
    use Exportable;

    protected $requestData;


    public function __construct($requestData)
    {
        $this->requestData = $requestData;
    }


    public function sheets(): array
    {
        return [
            new VerifiedPerDayExport($this->requestData),
            new VerifiedSummaryExports($this->requestData),
            // new PerGcTypeAndBuExports($this->requestData),
        ];
    }
}
