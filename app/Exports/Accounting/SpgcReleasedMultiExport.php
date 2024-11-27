<?php

namespace App\Exports\Accounting;

use App\Models\User;
use App\Services\Progress;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class SpgcReleasedMultiExport extends Progress implements WithMultipleSheets
{
    use Exportable;
    public function __construct(protected array $transactionDate, protected User $user)
    {
        parent::__construct();

        //Count Total Rows for Broadcasting
        $this->progress['progress']['totalRow'] += (new SpgcReleasedPerBarcode($transactionDate))->query()->count();
        $this->progress['progress']['totalRow'] += (new SpgcReleasedPerCustomer($transactionDate))->query()->get()->count();
    }

    public function sheets(): array
    {

        return [
            new SpgcReleasedPerBarcode($this->transactionDate, $this->progress, $this->reportId, $this->user),
            new SpgcReleasedPerCustomer($this->transactionDate, $this->progress, $this->reportId, $this->user),
        ];
    }
}
