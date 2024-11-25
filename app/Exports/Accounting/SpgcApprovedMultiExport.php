<?php

namespace App\Exports\Accounting;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class SpgcApprovedMultiExport implements WithMultipleSheets
{
    use Exportable;

    public function __construct(protected array $transactionDate){

    }

    public function sheets(): array
    {
        return [
            new SpgcApprovedExport($this->transactionDate),
        ];
    }
}
