<?php

namespace App\Exports\Accounting;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use App\Models\SpecialExternalGcrequestEmpAssign;
use Maatwebsite\Excel\Concerns\WithTitle;

class SpgcApprovedExport implements FromQuery, ShouldAutoSize, WithTitle
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
        DATE_FORMAT(special_external_gcrequest.spexgc_datereq, '%m/%d/%Y') as datereq,
        DATE_FORMAT(approved_request.reqap_date, '%m/%d/%Y') as daterel,
        CONCAT(reqby.firstname,' ',reqby.lastname) as trby,
        special_external_customer.spcus_acctname,
        special_external_customer.spcus_companyname
        ")
            ->where([
                ['approved_request.reqap_approvedtype', 'Special External GC Approved'],
                ['special_external_gcrequest_emp_assign.spexgc_status', '<>', 'inactive']
            ])
            ->whereBetween('approved_request.reqap_date', [$this->transactionDate['from'], $this->transactionDate['to']])
            ->orderBy('special_external_gcrequest.spexgc_datereq')
            ->join('special_external_gcrequest', 'special_external_gcrequest.spexgc_id', '=', 'special_external_gcrequest_emp_assign.spexgcemp_trid')
            ->join('approved_request', 'approved_request.reqap_trid', '=', 'special_external_gcrequest.spexgc_id')
            ->join('users as reqby', 'reqby.user_id', '=', 'special_external_gcrequest.spexgc_reqby')
            ->join('special_external_customer', 'special_external_customer.spcus_id', '=', 'special_external_gcrequest.spexgc_company')
            ->groupBy(
                'special_external_gcrequest.spexgc_datereq',
                'special_external_gcrequest.spexgc_num',
                'approved_request.reqap_date',
                'reqby.firstname',
                'reqby.lastname',
                'special_external_customer.spcus_acctname',
                'special_external_customer.spcus_companyname'
            );

    }
    public function title(): string
    {
        return 'Per Barcode';
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
