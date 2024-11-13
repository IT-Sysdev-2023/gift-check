<?php

namespace App\Exports\SPGCReleasedExcel;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;


class releasePerCustomer implements FromCollection, WithHeadings, WithTitle, WithStyles, ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */
    protected $releaseData;
    public function __construct($request)
    {
        $this->releaseData = $request;
    }
    public function headings(): array
    {
        return [
            'Date Requested',
            'Company',
            'Approval #',
            'Total Amount'
        ];
    }

    public function title(): string
    {
        return 'Per Customer';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => 'true', 'Sans-serif' => 'true']],
            'A1:Z1000' => ['alignment' => ['horizontal' => 'left']]
        ];
    }

    public function collection()
    {
        $perCustomer =  $this->releasedPerCustomerData($this->releaseData);
        return collect($perCustomer);
    }

    private function releasedPerCustomerData($request)
    {

        return DB::table('special_external_gcrequest_emp_assign')
            ->join('special_external_gcrequest', 'special_external_gcrequest.spexgc_id', '=', 'special_external_gcrequest_emp_assign.spexgcemp_trid')
            ->join('approved_request', 'approved_request.reqap_trid', '=', 'special_external_gcrequest.spexgc_id')
            ->join('users as reqby', 'reqby.user_id', '=', 'special_external_gcrequest.spexgc_reqby')
            ->join('special_external_customer', 'special_external_customer.spcus_id', '=', 'special_external_gcrequest.spexgc_company')
            ->where('approved_request.reqap_approvedtype', 'Special External GC Approved')
            ->where('special_external_gcrequest_emp_assign.spexgc_status', '!=', 'inactive')
            ->whereBetween(DB::raw("DATE_FORMAT(approved_request.reqap_date, '%Y-%m-%d')"), [$this->releaseData['startDate'], $this->releaseData['endDate']])
            ->select(
                DB::raw("DATE_FORMAT(special_external_gcrequest.spexgc_datereq, '%m/%d/%Y') as datereq"),
                'special_external_customer.spcus_companyname',
                'special_external_gcrequest.spexgc_num',
                DB::raw("IFNULL(SUM(special_external_gcrequest_emp_assign.spexgcemp_denom), 0.00) as totdenom"),
                // DB::raw("IFNULL(COUNT(special_external_gcrequest_emp_assign.spexgcemp_barcode), 0) as totcnt"),
                // DB::raw("DATE_FORMAT(approved_request.reqap_date, '%m/%d/%Y') as daterel"),
                // DB::raw("CONCAT(reqby.firstname, ' ', reqby.lastname) as trby"),
                // 'special_external_customer.spcus_acctname',
            )
            ->groupBy(
                'special_external_gcrequest.spexgc_num',
                'special_external_gcrequest.spexgc_datereq',
                'approved_request.reqap_date',
                'reqby.firstname',
                'reqby.lastname',
                'special_external_customer.spcus_acctname',
                'special_external_customer.spcus_companyname'
            )

            ->orderBy('special_external_gcrequest.spexgc_datereq')
            ->get();
    }
}
