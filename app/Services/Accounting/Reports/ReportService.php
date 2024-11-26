<?php

namespace App\Services\Accounting\Reports;

use App\Exports\Accounting\SpgcApprovedExport;

use App\Exports\Accounting\SpgcApprovedMultiExport;
use App\Jobs\Accounting\SpgcApprovedReport;
use App\Models\SpecialExternalGcrequestEmpAssign;
use App\Services\Documents\ExportHandler;
use App\Services\Documents\ImportHandler;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\DashboardRoutesTrait;


class ReportService
{
    use DashboardRoutesTrait;
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
    
    public function generatePdf()
    {

    }

    public function listOfReports(Request $request)
    {
        $getFiles = (new ImportHandler())->getFileReports($request->user()->usertype);
        return inertia('Treasury/Reports/GeneratedReports', [
            'files' => $getFiles
        ]);
    }
}