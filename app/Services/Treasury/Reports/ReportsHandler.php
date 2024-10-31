<?php

namespace App\Services\Treasury\Reports;

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

	public function __construct()
	{
	}
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
		$discount = $this->generateTotalTransDiscount($request);

		return (object) [

			'cashSales' => $cashSales,
			'cardSales' => $cardSales,

			'ar' => $ar,
			'totalArCustomer' => $this->generateCustomerDiscount($request),

			'totalTransactionDiscount' => $discount,
			'grandTotalNet' => ReportHelper::grandTotal($cashSales, $cardSales, $ar, $discount)
		];
	}
	protected function refund(Request $request)
	{

	}


}