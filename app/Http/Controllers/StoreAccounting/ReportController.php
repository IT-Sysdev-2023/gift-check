<?php

namespace App\Http\Controllers\StoreAccounting;

use App\Http\Controllers\Controller;
use App\Services\StoreAccounting\ReportService;
use Illuminate\Http\Request;


class ReportController extends Controller
{
    public function __construct(public ReportService $reportService) {
    }

    public function verifiedGcYearlySubmit(Request $request){
        // dd($request->all());
        return $this->reportService->verifiedGcYearlySubmit($request);
    }

    public function listOfGeneratedReports(Request $request){
        return $this->reportService->generatedReports($request);
    }

    public function generateStorePurchasedReport(Request $request)
    {
        return $this->reportService->billingReport($request);
    }

    public function redeemReportSubmit(Request $request)
    {
        return $this->reportService->redeemReport($request);
    }

}
