<?php

namespace App\Services\Treasury;

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

	public function generatePdf(Request $request)
	{

		$request->validate([
			"reportType" => 'required',
			"transactionDate" => "required",
			"store" => 'required',
			"date" => 'required_if:transactionDate,dateRange',
		]);

		$storeData = LazyCollection::make(function () use ($request) {

			//All Stores
			if ($request->store === 'all') {
				$store = Store::selectStore()->cursor();
				foreach ($store as $item) {
					yield $this->handleRecords($request, $item->value);
				}
			} else {
				yield $this->handleRecords($request, $request->store);
			}
		});

		$pdf = Pdf::loadView('pdf.treasuryReports', ['data' => ['stores' => $storeData]]);

		return $pdf->output();
	}

	private function handleRecords(Request $request, string $store)
	{

		$record = collect();
		$footerData = collect();

		$reportType = collect($request->reportType);

		$this->setStore($store)->setDateOfTransactions($request);

		if ($this->hasRecords($request)) {
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

			return 'error';
		}

		return [
			'header' => $this->pdfHeaderDate($request),
			'data' => [...$record],
			'footer' => [...$footerData],
		];
	}
}