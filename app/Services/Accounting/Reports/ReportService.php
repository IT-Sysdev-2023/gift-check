<?php

namespace App\Services\Accounting\Reports;

use App\Exports\Accounting\SpgcApprovedExport;
use App\Helpers\NumberHelper;
use App\Jobs\Accounting\SpgcApprovedReport;
use App\Models\SpecialExternalGcrequestEmpAssign;
use App\Services\Documents\ExportHandler;
use App\Services\Treasury\Reports\ReportHelper;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Date;

class ReportService
{
    public function specialGcReport(Request $request)
    {
        $request->validate([
            'date' => 'array'
        ]);
        ini_set('max_execution_time', 3600);
        ini_set('memory_limit', '-1');
        set_time_limit(3600);
        // SpgcApprovedReport::dispatch($request->only(['date', 'format']));
        // dd($this->handleRecords($request->date));
        $pdf = Pdf::loadView('pdf.accountingSpgcApprovedReport', ['data' => $this->handleRecords($request->date)]);
        $transDateHeader = ReportHelper::transactionDateLabel(true, $request->date);

        (new ExportHandler())
            ->setFolder('Reports')
            ->setSubfolderAsUsertype($request->user()->usertype)
            ->setFileName($request->user()->user_id, 1)
            // ->exportToExcel($this->dataForExcel($request->date));
            ->exportToPdf($pdf->output());


    }
    private function handleRecords($date)
    {
        $record = collect();

        $record->put('perCustomer', $this->perCustomerRecord($date));
        $record->put('perBarcode', $this->perBarcode($date));

        $header = collect([
            'reportCreated' => now()->toFormattedDateString(),
        ]);

        $transDateHeader = ReportHelper::transactionDateLabel(true, $date);

        $header->put('transactionDate', $transDateHeader);

        return [
            'header' => $header,
            'records' => $record,
        ];
    }
    private function perCustomerRecord($date)
    {
        $data = SpecialExternalGcrequestEmpAssign::selectRaw("
                    COALESCE(SUM(special_external_gcrequest_emp_assign.spexgcemp_denom), 0) AS totDenom,
                    COALESCE(COUNT(special_external_gcrequest_emp_assign.spexgcemp_barcode), 0) AS totcnt,
                    special_external_gcrequest.spexgc_num,
                    special_external_gcrequest.spexgc_datereq as datereq,
                    approved_request.reqap_date as daterel,
                    special_external_customer.spcus_acctname
            ")
            ->joinDataAndGetOnTables()
            ->specialApproved($date)
            ->groupBy(
                'special_external_gcrequest.spexgc_datereq',
                'special_external_gcrequest.spexgc_num',
                'approved_request.reqap_date',
                'special_external_customer.spcus_acctname',
            )
            ->orderBy('special_external_gcrequest.spexgc_datereq')
            ->cursor();

        return $data->map(function ($item) {

            $item->datereq = Date::parse($item->datereq)->toFormattedDateString();
            $item->daterel = Date::parse($item->daterel)->toFormattedDateString();
            $item->totDenom = NumberHelper::currency($item->totDenom);
            return $item;
        });

    }

    public function perBarcode($date)
    {
        $data = SpecialExternalGcrequestEmpAssign::select(
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
            ->specialApproved($date)
            ->orderBy('special_external_gcrequest_emp_assign.spexgcemp_barcode')
            ->cursor();

        $count = 1;
        return $data->map(function ($item) use (&$count) {
            $item->count = $count++;
            $item->datereq = Date::parse($item->datereq)->toFormattedDateString();
            $item->daterel = Date::parse($item->daterel)->toFormattedDateString();
            $item->spexgcemp_denom = NumberHelper::currency($item->spexgcemp_denom);
            $item->fullName = $item->spexgcemp_lname . ', ' . $item->spexgcemp_fname;
            return $item;
        });
    }
    private function dataForExcel(array $transactionDate)
    {
        return new SpgcApprovedExport($transactionDate);
    }
    public function generatePdf()
    {

    }
}