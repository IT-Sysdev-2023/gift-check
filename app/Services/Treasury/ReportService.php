<?php

namespace App\Services\Treasury;

use App\Helpers\NumberHelper;
use App\Models\Denomination;
use App\Models\TransactionStore;
use App\Services\Treasury\Reports\ReportHelper;
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
			"reportType" => 'required',
			"transactionDate" => "required",
			"store" => 'required',
			"date" => 'required_if:transactionDate,dateRange',
		]);

		$this->setDateOfTransactions($request);
		if ($this->hasRecords($request)) {
			$gcSales = $this->gcSales($request);
			// $refund = $this->refund($request);
		}

		$data = [
			//Header
			'header' => $this->pdfHeaderDate($request),
			//Body
			'data' => [
				'cashSales' => $gcSales->cashSales,
				'totalCashSales' => NumberHelper::currency($gcSales->cashSales->sum('net')),

				'cardSales' => $gcSales->cardSales,
				'totalCardSales' => NumberHelper::currency($gcSales->cardSales->sum('net')),

				'ar' => $gcSales->ar,
				'totalCustomerDiscount' => NumberHelper::currency($gcSales->totalArCustomer),
				'totalAr' => NumberHelper::currency($gcSales->ar->sum('net')),
			],
			//Footer
			'footer' => [
				'totalTransactionDiscount' => NumberHelper::currency($gcSales->totalTransactionDiscount),
				'grandTotalNet' => NumberHelper::currency($gcSales->grandTotalNet),
			],

		];
		$pdf = Pdf::loadView('pdf.treasuryReports', ['data' => $data]);

		return $pdf->output();
	}
}