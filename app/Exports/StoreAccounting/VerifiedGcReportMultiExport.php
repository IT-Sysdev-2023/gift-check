<?php

namespace App\Exports\StoreAccounting;

use App\Models\User;
use App\Services\Progress;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class VerifiedGcReportMultiExport extends Progress implements WithMultipleSheets
{
    use Exportable;
    public function __construct(protected array $requirements, protected User $user)
    {
        parent::__construct();
        // $this->progress['name'] = "Excel SPGC Approved Report";

        // //Count Total Rows for Broadcasting
        // $this->progress['progress']['totalRow'] += (new SpgcApprovedPerBarcode($transactionDate))->query()->count();
        // $this->progress['progress']['totalRow'] += (new SpgcApprovedPerCustomer($transactionDate))->query()->get()->count();
    }

    public function sheets(): array
    {
        return [
            new VerifiedGcReportPerDay($this->requirements, $this->progress, $this->reportId, $this->user),
            // new SpgcApprovedPerCustomer($this->transactionDate, $this->progress, $this->reportId, $this->user),
        ];
    }
}
