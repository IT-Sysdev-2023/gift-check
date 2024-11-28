<?php

namespace App\Services\Accounting\Reports;

use App\Exports\Accounting\SpgcApprovedExport;

use App\Exports\Accounting\SpgcApprovedMultiExport;
use App\Jobs\Accounting\SpgcApprovedReport;
use App\Jobs\Accounting\SpgcReleasedReport;
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

        SpgcApprovedReport::dispatch($request->only(['date', 'format']));
    }
    
    public function specialGcReleasedReport(Request $request)
    {
        $request->validate([
            'date' => 'array'
        ]);

        SpgcReleasedReport::dispatch($request->only(['date', 'format']));
    }

    public function listOfReports(Request $request)
    {
        $getFiles = (new ImportHandler())->getFileReports($request->user()->usertype);
        return inertia('Treasury/Reports/GeneratedReports', [
            'files' => $getFiles
        ]);
    }
}