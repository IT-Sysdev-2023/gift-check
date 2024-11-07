<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\IadVerifiedExports\VerifiedPerDayExport;

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
        ];
    }
}
