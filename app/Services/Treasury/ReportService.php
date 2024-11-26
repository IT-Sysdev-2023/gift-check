<?php

namespace App\Services\Treasury;

use App\Events\TreasuryReportEvent;
use App\Jobs\EodReport;
use App\Jobs\GcReport;
use App\Models\StoreEod;
use App\Services\Documents\FileHandler;
use App\Services\Documents\ImportHandler;
use App\Services\Treasury\Reports\ReportsHandler;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Models\Store;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\Str;
use App\DashboardRoutesTrait;

class ReportService extends ReportsHandler
{
	use DashboardRoutesTrait;

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

		$this->setDateOfTransactionsEod($request);

		if (!$this->hasEodRecords($request->user())) {
			return response()->json(['error' => 'No Transaction For this Date']);
		}
		EodReport::dispatch($request->all());
		// return $pdf->output();
	}

	public function generatedReports(Request $request)
	{
		$getFiles = (new ImportHandler())->getFileReports($request->user()->usertype);
		return inertia('Treasury/Reports/GeneratedReports', [
			'files' => $getFiles
		]);
	}
	public function download(Request $request)
	{
		return (new ImportHandler())
			->setFolder('Reports')
			->downloadFile($request->file, true);
	}
}