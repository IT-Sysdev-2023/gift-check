<?php

namespace App\Exports\SPGCVarianceExcel;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class VarianceCombinationExcel implements WithMultipleSheets
{
    /**
    * @return \Illuminate\Support\Collection
    */

    protected $allVarianceExcel;

    public function __construct($request){
        $this->allVarianceExcel = $request;
        // dd($this->allVarianceExcel = $request);
    } 

    public function sheets(): array
    {
        // dd($this->allVarianceExcel);
        return [
            new VarianceExcel($this->allVarianceExcel),
            new varianceTalibonExcel($this->allVarianceExcel)
        ];
    }
}
