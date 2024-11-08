<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Services\Treasury\ReportService;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    public function __construct(public ReportService $reportService){

    }
    public function reports()
    {
        $store = Store::selectStore()->get();
        return inertia('Treasury/Reports', [
            'title' => 'Reports',
            'store' => $store->push(['value' => 'all', 'label' => 'All Stores']),
        ]);
    }
    public function generateReports(Request $request)
    {
        return $this->reportService->generatePdf($request);
    }
}
