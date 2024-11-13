<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\StoreEod;
use App\Services\Treasury\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;

class ReportsController extends Controller
{
    public function __construct(public ReportService $reportService){

    }
    public function gcReport()
    {
        $store = Store::selectStore()->get();
        return inertia('Treasury/Reports/GcReport', [
            'title' => 'Reports',
            'store' => $store->push(['value' => 'all', 'label' => 'All Stores']),
        ]);
    }
    public function eodReport(){
        return inertia('Treasury/Reports/EodReport', [
            'title' => 'Reports',
        ]);
    }
    public function generateGcReports(Request $request)
    {
        return $this->reportService->generateGcPdf($request);
    }
    public function generateEodReports(Request $request){
        return $this->reportService->generateEodPdf($request);
    }
}
