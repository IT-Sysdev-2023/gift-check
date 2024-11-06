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
				$store = Store::select('store_id as value')->where('store_status', 'active')->cursor();

				foreach ($store as $item) {
					$this->setStore($item->value)->setDateOfTransactions($request);
					$record = $this->handleRecords($request);

					yield [
						'header' => $this->pdfHeaderDate($request),
						'data' => [...$record['data']],
						'footer' => [...$record['footer']],
					];
				}
			} else {
				$this->setStore($request->store)->setDateOfTransactions($request);
				$record = $this->handleRecords($request);

				yield [
					'header' => $this->pdfHeaderDate($request),
					'data' => [...$record['data']],
					'footer' => [...$record['footer']],
				];
			}
		});

		$pdf = Pdf::loadView('pdf.treasuryReports', ['data' => ['stores' => $storeData]]);

		return $pdf->output();
	}

	private function handleRecords(Request $request)
	{

		$record = collect();
		$footerData = collect();

		$reportType = collect($request->reportType);

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
			return response()->json('No Transaction at this moment!');
		}

		return [
			'data' => $record,
			'footer' => $footerData
		];
	}
}