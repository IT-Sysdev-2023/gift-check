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



class ReportsHandler extends ReportGenerator
{
	const SALE_TYPE_CASH = 1;
	const SALE_TYPE_CARD = 2;
	const SALE_TYPE_AR = 3;

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

	protected function gcRevalidation()
	{
		$records = $this->revalidation();
		if (!is_null($records)) {
			return [
				'totalRevalidationPayment' => NumberHelper::currency($records)
			];
		}
		return ['gcRevalidation' => 'No GC Revalidation Transaction'];
	}
	protected function refund(Request $request)
	{
		$refunds = $this->fundsRecords();
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
		$discount = $this->generateTotalTransDiscount();
		$grandTotal = ReportHelper::grandTotal($gcSales['cashSales'], $gcSales['cardSales'], $gcSales['ar'], $discount);

		return [
			'totalTransactionDiscount' => NumberHelper::currency($discount),
			'grandTotalNet' => NumberHelper::currency($grandTotal),
		];
	}


}