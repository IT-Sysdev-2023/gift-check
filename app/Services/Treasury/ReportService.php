<?php

namespace App\Services\Treasury;

use App\Events\TreasuryReportEvent;
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

		//Dont touch this otherwise you're f*cked!
		//Dont put this in LazyCollection otherwise the realtime fire twice
		$storeData = [];

		//All Stores
		if ($request->store === 'all') {
			$store = Store::selectStore()->cursor();

			$percentage = 1;

			$this->progress['progress']['totalRow'] = count($store);

			foreach ($store as $item) {
				$this->progress['progress']['currentRow'] = $percentage++;
				TreasuryReportEvent::dispatch($request->user(), $this->progress);

				$storeData[] = $this->handleRecords($request, $item->value);
			}
		} else {
			$storeData[] = $this->handleRecords($request, $request->store);
		}

		$pdf = Pdf::loadView('pdf.treasuryReport', ['data' => ['stores' => $storeData]]);

		return $pdf->output();
	}

	public function generateEodPdf(Request $request)
	{

		$request->validate([
			"transactionDate" => "required",
			"date" => 'required_if:transactionDate,dateRange',
		]);

		$storeData = [];

		$storeData = $this->handleEodRecords($request);

		if ($storeData === 'error') {
			return response()->json(['message' => 'No Record Found in selected transaction date'], 404);
		}
		$pdf = Pdf::loadView('pdf.treasuryEodReport', ['data' => $storeData]);

		return $pdf->output();
	}
	private function handleRecords(Request $request, string $store)
	{
		$record = collect();
		$footerData = collect();

		$reportType = collect($request->reportType);

		$this->setStore($store)->setDateOfTransactions($request);

		if (!is_null($this->transactionDate) && $this->hasRecords($request)) {

			if ($reportType->contains('gcSales')) {
				$record->put('gcSales', $this->gcSales());
				$footerData->put('gcSalesFooter', $this->footer());
			}

			if ($reportType->contains('refund')) {
				$footerData->put('refundFooter', $this->refund());
			}
			if ($reportType->contains('gcRevalidation')) {
				$footerData->put('revalidationFooter', $this->gcRevalidation());
			}
		} else {
			$this->dispatchProgress(3);
			return [
				'error' => 'No Transactions'
			];
		}

		return [
			'header' => $this->pdfHeaderDate($request),
			'data' => [...$record],
			'footer' => [...$footerData],
		];



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