<?php

namespace App\Http\Controllers\StoreAccounting;

use App\Http\Controllers\Controller;
use App\Services\StoreAccounting\ReportService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BillingExcelReport\BillingExcelReportPerDay;


class ReportController extends Controller
{
    public function __construct(public ReportService $reportService)
    {
    }

    public function verifiedGcYearlySubmit(Request $request)
    {
        // dd($request->all());
        return $this->reportService->verifiedGcYearlySubmit($request);
    }

    public function listOfGeneratedReports(Request $request)
    {
        return $this->reportService->generatedReports($request);
    }

    public function generateStorePurchasedReport(Request $request)
    {
        return $this->reportService->billingReport($request);
    }

    public function redeemReportSubmit(Request $request)
    {
        // dd($request->all());
        return $this->reportService->redeemReport($request);
    }

    public function verifiedStoreSubmit(Request $request)
    {
        $request->validate([
            'year' => 'required',
            'store' => 'required',
            'type' => 'required',
        ]);



        dd($request->all());
    }

    public function billingReportPerDay(Request $request)
    {
        // dd($request->all());
        return $this->reportService->billingReportPerDay($request);
    }
    public function generateBillingPerDayReport(Request $request)
    {
        // dd($request->all());
        return Excel::download(new BillingExcelReportPerDay($request->toArray()), 'Billing per day report.xlsx');
    }
}
