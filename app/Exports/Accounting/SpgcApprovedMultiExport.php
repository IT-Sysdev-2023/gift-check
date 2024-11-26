<?php

namespace App\Exports\Accounting;

use App\Models\SpecialExternalGcrequestEmpAssign;
use App\Models\User;
use App\Services\Progress;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Illuminate\Support\Facades\Auth;

class SpgcApprovedMultiExport extends Progress implements WithMultipleSheets
{
    use Exportable;
    public function __construct(protected array $transactionDate, protected User $user)
    {
        parent::__construct();
        
        //BroadCasting
        $this->progress['progress']['totalRow'] += (new SpgcApprovedPerBarcode($transactionDate, $this->progress, $this->reportId, $user))->query()->count();
        $this->progress['progress']['totalRow'] += (new SpgcApprovedPerCustomer($transactionDate, $this->progress, $this->reportId, $user))->query()->get()->count();
    }

    public function sheets(): array
    {
        $perBarcode = new SpgcApprovedPerBarcode($this->transactionDate, $this->progress, $this->reportId, $this->user);
        $perCustomer = new SpgcApprovedPerCustomer($this->transactionDate, $this->progress, $this->reportId, $this->user);

        return [
            $perBarcode,
            $perCustomer,
        ];
    }
}
