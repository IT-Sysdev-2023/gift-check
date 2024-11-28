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
            'date' => 'array',
        ]);

        if (!self::hasApprovedSpgcRecord($request)) {
            return response()->json(['error' => 'No Record Found on this date'], 404);
        }
        SpgcApprovedReport::dispatch($request->only(['date', 'format']));
    }

    public function specialGcReleasedReport(Request $request)
    {
        $request->validate([
            'date' => 'array'
        ]);

        if (!self::hasReleasedSpgcRecord($request)) {
            return response()->json(['error' => 'No Record Found on this date'], 404);
        }

        SpgcReleasedReport::dispatch($request->only(['date', 'format']));
    }

    public function listOfReports(Request $request)
    {
        $getFiles = (new ImportHandler())->getFileReports($request->user()->usertype);
        return inertia('Treasury/Reports/GeneratedReports', [
            'files' => $getFiles
        ]);
    }

    private static function hasApprovedSpgcRecord(Request $request)
    {

        $customer = SpecialExternalGcrequestEmpAssign::joinDataAndGetOnTables()
            ->specialApproved($request->date)
            ->groupBy(
                'special_external_gcrequest.spexgc_datereq',
                'special_external_gcrequest.spexgc_num',
                'approved_request.reqap_date',
                'special_external_customer.spcus_acctname',
            )
            ->exists();

        $barcode = SpecialExternalGcrequestEmpAssign::joinDataBarTables()
            ->specialApproved($request->date)
            ->exists();

        return $customer || $barcode;
    }

    private static function hasReleasedSpgcRecord(Request $request)
    {

        $customer = SpecialExternalGcrequestEmpAssign::joinDataAndGetOnTables()
            ->specialReleased($request->date)
            ->groupBy(
                'special_external_gcrequest.spexgc_datereq',
                'special_external_gcrequest.spexgc_num',
                'approved_request.reqap_date',
                'special_external_customer.spcus_acctname',
            )
            ->exists();

        $barcode = SpecialExternalGcrequestEmpAssign::joinDataBarTables()
            ->specialReleased($request->date)
            ->exists();

        return $customer || $barcode;
    }
}