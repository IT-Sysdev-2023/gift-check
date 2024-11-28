<?php

namespace App\Exports\Accounting;

use App\Events\AccountingReportEvent;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use App\Models\SpecialExternalGcrequestEmpAssign;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SpgcApprovedPerCustomer implements FromQuery, ShouldAutoSize, WithTitle, WithHeadings, WithMapping, WithStyles
{

    public function __construct(protected array $transactionDate, protected &$progress = null, protected $reportId = null, protected $user= null)
    {
    }
    public function query()
    {
        return SpecialExternalGcrequestEmpAssign::query()->selectRaw("
        COALESCE(SUM(special_external_gcrequest_emp_assign.spexgcemp_denom), 0) AS totDenom,
        COALESCE(COUNT(special_external_gcrequest_emp_assign.spexgcemp_barcode), 0) AS totcnt,
        special_external_gcrequest.spexgc_num,
        special_external_gcrequest.spexgc_datereq as datereq,
        approved_request.reqap_date as daterel,
        special_external_customer.spcus_acctname
")
            ->joinDataAndGetOnTables()
            ->specialApproved($this->transactionDate)
            ->groupBy(
                'special_external_gcrequest.spexgc_datereq',
                'special_external_gcrequest.spexgc_num',
                'approved_request.reqap_date',
                'special_external_customer.spcus_acctname',
            )
            ->orderBy('special_external_gcrequest.spexgc_datereq');

    }
    public function map($data): array
    {
        $this->broadcastProgress("Generating Customer Records");
        return [
            (new \DateTime($data->datereq))->format('F j, Y'),
            $data->spcus_acctname,
            $data->spexgc_num,
            $data->totDenom,
        ];
    }

    public function countRecords(){
        return $this->query()->count();
    }
    
    public function title(): string
    {
        return 'Per Customer';
    }

    public function headings(): array
    {
        return [
            'DATE REQUESTED',
            'COMPANY',
            'APPROVAL #',
            'TOTAL AMOUNT',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
    private function broadcastProgress(string $info)
    {
        $this->progress['info'] = $info;
        $this->progress['progress']['currentRow']++;
        AccountingReportEvent::dispatch($this->user, $this->progress, $this->reportId);
    }
}
