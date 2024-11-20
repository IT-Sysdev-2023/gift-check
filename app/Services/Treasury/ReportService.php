<?php

namespace App\Services\Treasury;

use App\Events\TreasuryReportEvent;
use App\Jobs\GcReport;
use App\Models\StoreEod;
use App\Services\Treasury\Reports\ReportsHandler;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Models\Store;
use Illuminate\Support\LazyCollection;

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

	public function generateGcPdf(Request $request)
	{
		$request->validate([
			"reportType" => 'required',
			"transactionDate" => "required",
			"store" => 'required',
			"date" => 'required_if:transactionDate,dateRange',
		]);
		GcReport::dispatch($request->all());
	}

	public function generateEodPdf(Request $request)
	{

		$request->validate([
			"transactionDate" => "required",
			"date" => 'required_if:transactionDate,dateRange',
		]);
		$storeData = $this->handleEodRecords($request);

		if ($storeData === 'error') {
			return response()->json(['message' => 'No Record Found in selected transaction date'], 404);
		}
		$pdf = Pdf::loadView('pdf.treasuryEodReport', ['data' => $storeData]);

		return $pdf->output();
	}
	
	private function handleEodRecords(Request $request)
	{
		$record = collect();

		$this->setDateOfTransactionsEod($request);

		if ($this->hasEodRecords($request)) {
			$record->put('records', $this->eodRecords());
		} else {
			return 'error';
		}

		return [
			'header' => $this->pdfEodHeaderDate(),
			...$record,
		];
	}
}