<?php

namespace App\Services\Treasury\Reports;

use App\Helpers\NumberHelper;
use App\Models\TransactionPayment;
use App\Models\TransactionRefund;
use App\Models\TransactionRefundDetail;
use App\Models\TransactionStore;

use Illuminate\Support\Facades\Date;
use Illuminate\Support\Benchmark;
use Illuminate\Http\Request;
use App\Models\Store;


class ReportsHandler extends ReportGenerator
{
	const SALE_TYPE_CASH = 1;
	const SALE_TYPE_CARD = 2;
	const SALE_TYPE_AR = 3;

	protected function pdfHeaderDate(Request $request)
	{
		if ($request->store !== 13) { //if is not all Store
			$store = Store::where('store_id', $request->store)->value('store_name');
		}

		$header = collect([
			'reportCreated' => now()->toFormattedDateString(),
			'store' => $store,
			'reportType' => $request->reportType
		]);

		$transDateHeader = match ($request->transactionDate) {
			'dateRange' => ReportHelper::extractDateRange($request)->from . 'to' . ReportHelper::extractDateRange($request)->to,
			'today' => now()->toFormattedDateString(),
			'yesterday' => Date::yesterday()->toFormattedDateString(),
			'thisWeek' => now()->startOfWeek()->toFormattedDateString() . ' to ' . now()->endOfWeek()->toFormattedDateString(),
			'currentMonth' => now()->startOfMonth()->toFormattedDateString() . ' to ' . now()->endOfMonth()->toFormattedDateString(),
			'allTransactions' => is_null(ReportHelper::allTransaction($request))
			? 'No Transactions'
			: Date::parse(ReportHelper::allTransaction($request)[0])->toFormattedDateString() .
			' - ' . Date::parse(ReportHelper::allTransaction($request)[1])->toFormattedDateString()
		};

		$header->put('transactionDate', $transDateHeader);

		return $header;
	}
	protected function gcSales(Request $request)
	{
		$cashSales = $this->generateSalesData($request, self::SALE_TYPE_CASH);
		$cardSales = $this->generateSalesData($request, self::SALE_TYPE_CARD);
		$ar = $this->generateSalesData($request, self::SALE_TYPE_AR);

		return [

			'cashSales' => $cashSales,
			'totalCashSales' => NumberHelper::currency($cashSales->sum('net')),

			'cardSales' => $cardSales,
			'totalCardSales' => NumberHelper::currency($cardSales->sum('net')),

			'ar' => $ar,
			'totalCustomerDiscount' => NumberHelper::currency($this->generateCustomerDiscount($request)),
			'totalAr' => NumberHelper::currency($ar->sum('net')),

		];
	}

	protected function gcRevalidation(Request $request)
	{
		$records = $this->revalidation($request);
		if (!is_null($records)) {
			return [
				'totalRevalidationPayment' => NumberHelper::currency($records)
			];
		}
		return ['gcRevalidation' => 'No GC Revalidation Transaction'];
	}
	protected function refund(Request $request)
	{
		$refunds = $this->fundsRecords($request);
		$serviceCharge = $this->serviceCharge($request);
		if (!is_null($refunds)) {
			$denomination = bcsub((string) $refunds->denomination_sum_denomination, (string) $refunds->lindisc, 2);
			$total = bcsub($denomination, (string) $refunds->trdisc, 2);
			$charge = bcsub($total, (string) $serviceCharge->scharge, 2);

			return [
				'refund' => $refunds->denomination_sum_denomination,
				'totalLineDiscount' => $refunds->lindisc,
				'totalTransactionsDiscount' => $refunds->trdisc,
				'totalServiceCharge' => $serviceCharge->scharge,
				'totalRefund' => $charge
			];
		}
		return ['refund' => 'No Refund Transaction'];
	}

	protected function footer(Request $request)
	{
		$gcSales = $this->gcSales($request);
		$discount = $this->generateTotalTransDiscount($request);
		$grandTotal = ReportHelper::grandTotal($gcSales['cashSales'], $gcSales['cardSales'], $gcSales['ar'], $discount);

		return [
			'totalTransactionDiscount' => NumberHelper::currency($discount),
			'grandTotalNet' => NumberHelper::currency($grandTotal),
		];
	}


}