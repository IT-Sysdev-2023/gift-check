<?php

namespace App\Exports\Accounting;

use App\Models\User;
use App\Services\Progress;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class SpgcApprovedMultiExport extends Progress implements WithMultipleSheets
{
    use Exportable;
    public function __construct(protected array $transactionDate, protected User $user)
    {
        parent::__construct();

        //Count Total Rows for Broadcasting
        $this->progress['progress']['totalRow'] += (new SpgcApprovedPerBarcode($transactionDate))->query()->count();
        $this->progress['progress']['totalRow'] += (new SpgcApprovedPerCustomer($transactionDate))->query()->get()->count();
    }

    public function sheets(): array
    {

        return [
            new SpgcApprovedPerBarcode($this->transactionDate, $this->progress, $this->reportId, $this->user),
            new SpgcApprovedPerCustomer($this->transactionDate, $this->progress, $this->reportId, $this->user),
        ];
    }
}
