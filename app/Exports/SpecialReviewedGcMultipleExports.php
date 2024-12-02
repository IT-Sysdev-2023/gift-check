<?php

namespace App\Exports;

use App\Exports\IadSpecialReviewed\SpecialReviewedExportsPerBarcode;
use App\Exports\IadSpecialReviewed\SpecialReviewedExportsPerCustomer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class SpecialReviewedGcMultipleExports implements WithMultipleSheets
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $requestData;


    public function __construct($requestData)
    {
        $this->requestData = $requestData;
    }

    public function sheets(): array
    {
        return [
            new SpecialReviewedExportsPerBarcode($this->requestData),
            new SpecialReviewedExportsPerCustomer($this->requestData),
        ];
    }
}
