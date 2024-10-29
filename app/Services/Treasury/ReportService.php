<?php

namespace App\Services\Treasury;

use App\Models\Denomination;
use App\Models\TransactionStore;
use App\Services\Treasury\Reports\ReportsHandler;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Benchmark;
use Illuminate\Http\Request;
use App\Models\Store;

class ReportService extends ReportsHandler
{
	public static function reports() //storesalesreport.php
	{

		$record = Store::select('store_id', 'store_name', 'default_password', 'company_code', 'store_code', 'issuereceipt')
			->where('store_status', 'active')
			->cursorPaginate()
			->withQueryString();

		return $record;
	}

	public function generatePdf(Request $request)
	{

		$request->validate([
			// "reportType" => 'required',
			// "transactionDate" => "required",
			"store" => 'required',
			// "date" => 'required_if:transactionDate,dateRange',
		]);
		dd($this->dataForPdf($request));
		if($this->isExists($request)){

			
			$cashSales = $this->dataForPdf($request);
		}
		// dd($this->dataForPdf($request));

		$data = [

			//Header
			'header' => $this->pdfHeaderDate($request),

			//Body
			'data' => [
				'cashSales' => $cashSales
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