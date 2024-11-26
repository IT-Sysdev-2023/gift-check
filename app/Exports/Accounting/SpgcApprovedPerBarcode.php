<?php

namespace App\Exports\Accounting;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use App\Models\SpecialExternalGcrequestEmpAssign;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SpgcApprovedPerBarcode implements FromQuery, ShouldAutoSize, WithTitle, WithHeadings, WithMapping, WithStyles
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
    public function map($data): array
    {
        return [
            (new \DateTime($data->datereq))->format('F j, Y'),
            $data->spexgcemp_barcode,
            $data->spexgcemp_denom,
            $data->spexgcemp_lname . ', ' . $data->spexgcemp_fname,
            $data->voucher,
            $data->spexgc_num,
            (new \DateTime($data->daterel))->format('F j, Y'),
        ];
    }
    public function title(): string
    {
        return 'Per Barcode';
    }

    public function headings(): array
    {
        return [
            'TRANSACTION DATE',
            'BARCODE',
            'DENOMINATION',
            'CUSTOMER',
            'VOUCHER',
            'APPROVAL #',
            'DATE APPROVED',
        ];
    }
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
