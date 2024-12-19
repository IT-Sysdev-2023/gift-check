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
        return $this->reportService->verifiedGcYearlySubmit($request);
    }

   
}
