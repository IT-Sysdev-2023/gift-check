<?php

namespace App\Exports\Accounting;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use App\Models\SpecialExternalGcrequestEmpAssign;
use Maatwebsite\Excel\Concerns\WithTitle;

class SpgcApprovedPerBarcode implements FromQuery, ShouldAutoSize, WithTitle
{

    public function __construct(protected array $transactionDate)
    {

    }
    public function query()
    {
        return SpecialExternalGcrequestEmpAssign::query()->select(
            'special_external_gcrequest_emp_assign.spexgcemp_denom',
            'special_external_gcrequest_emp_assign.spexgcemp_fname',
            'special_external_gcrequest_emp_assign.spexgcemp_lname',
            'special_external_gcrequest_emp_assign.spexgcemp_mname',
            'special_external_gcrequest_emp_assign.voucher',
            'special_external_gcrequest_emp_assign.spexgcemp_extname',
            'special_external_gcrequest_emp_assign.spexgcemp_barcode',

            'special_external_gcrequest.spexgc_num',
            'special_external_gcrequest.spexgc_datereq as datereq',
            'approved_request.reqap_date as daterel'
        )
            ->joinDataBarTables()
            ->specialApproved($this->transactionDate)
            ->orderBy('special_external_gcrequest_emp_assign.spexgcemp_barcode');

    }
    public function title(): string
    {
        return 'Per Barcode';
    }

    public function headings(): array
    {
        return [
            'Transaction Date',
            'Barcode',
            'Denomination',
            'Customer',
            'Voucher',
            'Approval #',
            'Date Approved',
        ];
    }
}
