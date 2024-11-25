<?php

namespace App\Http\Controllers;

use App\Services\Accounting\Reports\ReportService;
use Illuminate\Http\Request;

class AccountingReportController extends Controller
{

    public function __construct(public ReportService $reportService){

    }
    public function spgcApprovedReport(){
        return inertia('Accounting/Reports/SpecialGcApprovedReport', [
            'title' => 'Special Gc Report'
        ]);
    }

    public function generateApprovedReport(Request $request){
        return $this->reportService->specialGcReport($request);
    }
    public function spgcReleasedReport(){

    }
    public function generatedReports(Request $request){
        return $this->reportService->listOfReports($request);
    }
}
