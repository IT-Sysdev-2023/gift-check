<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Response;

class ReportsController extends Controller
{
    public function reports()
    {
        $store = Store::select('store_id as value', 'store_name as label')->where('store_status', 'active')->get();
        return inertia('Treasury/Reports', [
            'title' => 'Reports',
            'store' => $store->push(['value' => 13, 'label' => 'All Stores']),
        ]);
    }
    public function generateReports(Request $request)
    {
        $request->validate([
            // "reportType" => 'required',
            // "transactionDate" => "required",
            "store" => 'required',
            // "date" => 'required_if:transactionDate,dateRange',
        ]);

        if($request->store !== 13){ //if is not all Store
            $store = Store::where('store_id', $request->store)->value('store_name');
            // dd($store);
        }


        $header = collect([['reportCreated' => now()->toFormattedDateString()], ['store' => $store,]]);


        if($request->transactionDate === 'dateRange'){
            $from = Date::parse($request->date[0]);
            $to = Date::parse($request->date[0]);
            $header->push([
                'transactionDate' => "{$from} to {$to}"
            ]);
        }

        // dd($header);
        $data = [
            //Header
            'header' => [
                
            ],

            //Footer
            'footer' => [
                'totalTransactionDiscount' => 0,
                'grandTotalNet' => 0,
            ],
            
        ];
        $pdf = Pdf::loadView('pdf.treasuryReports', ['data' => $data]);

        return $pdf->output();
        // return Response::make($pdfContent, 200, [
        //     'Content-Type' => 'application/pdf',
        //     'Content-Disposition' => 'attachment; filename="treasuryReports.pdf"',
        // ]);
        // dd(1);
    }
}
