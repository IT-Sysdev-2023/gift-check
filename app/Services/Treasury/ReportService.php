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

		$record = collect();
		$footerData = collect();
		$reportType = collect($request->reportType);
		
		if ($this->hasRecords($request)) {
			if ($reportType->contains('gcSales')) {
				$record->put('gcSales', $this->gcSales($request));
				$footerData->put('gcSalesFooter', $this->footer($request));
			}
			if ($reportType->contains('refund')) {
				$footerData->put('refundFooter', $this->refund($request));
			}
			if ($reportType->contains('gcRevalidation')) {
				$footerData->put('revalidationFooter', $this->gcRevalidation($request));
			}
		}
		
		$data = [
			//Header
			'header' => $this->pdfHeaderDate($request),
			//Body
			'data' => [
				...$record,
			],
			//Footer
			'footer' => [
				...$footerData
			],

		];
		$pdf = Pdf::loadView('pdf.treasuryReports', ['data' => $data]);

		return $pdf->output();
	}
}