<?php

namespace App\Exports\Accounting;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use App\Models\SpecialExternalGcrequestEmpAssign;
use Maatwebsite\Excel\Concerns\WithTitle;

class SpgcApprovedPerCustomer implements FromQuery, ShouldAutoSize, WithTitle
{

    public function __construct(protected array $transactionDate)
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
    public function title(): string
    {
        return 'Per Customer';
    }

    public function headings(): array
    {
        return [
            'Date Requested',
            'Company',
            'Approval #',
            'Total Amount',
        ];
    }
}
