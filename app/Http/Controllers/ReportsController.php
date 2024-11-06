<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Services\Treasury\ReportService;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Response;

class ReportsController extends Controller
{
    public function __construct(public ReportService $reportService){

    }
    public function reports()
    {
        $store = Store::select('store_id as value', 'store_name as label')->where('store_status', 'active')->get();
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
