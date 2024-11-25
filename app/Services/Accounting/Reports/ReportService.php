<?php

namespace App\Services\Accounting\Reports;

use App\Exports\Accounting\SpgcApprovedExport;

use App\Jobs\Accounting\SpgcApprovedReport;
use App\Models\SpecialExternalGcrequestEmpAssign;
use App\Services\Documents\ExportHandler;
use App\Services\Treasury\Reports\ReportHelper;
use Illuminate\Http\Request;


class ReportService
{
    public function specialGcReport(Request $request)
    {
        $request->validate([
            'date' => 'array'
        ]);
        // ini_set('max_execution_time', 3600);
        // ini_set('memory_limit', '-1');
        // set_time_limit(3600);

        SpgcApprovedReport::dispatch($request->only(['date', 'format']));
    }
   
    private function dataForExcel(array $transactionDate)
    {
        return new SpgcApprovedExport($transactionDate);
    }
    public function generatePdf()
    {

    }
}