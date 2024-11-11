<?php

namespace App\Services\Treasury\Reports;

use App\Helpers\NumberHelper;

class ReportsHandler extends ReportGenerator
{
	const SALE_TYPE_CASH = 1;
	const SALE_TYPE_CARD = 2;
	const SALE_TYPE_AR = 3;

	private $cashSales;
	private $cardSales;
	private $ar;

	protected function gcSales()
	{
		$this->dispatchProgress(ReportHelper::GENERATING_SALES_DATA);
		$this->cashSales = $this->generateSalesData(self::SALE_TYPE_CASH);
		$this->cardSales = $this->generateSalesData(self::SALE_TYPE_CARD);
		$this->ar = $this->generateSalesData(self::SALE_TYPE_AR);

		return [

			'cashSales' => $this->cashSales,
			'totalCashSales' => NumberHelper::currency($this->cashSales->sum('net')),

			'cardSales' => $this->cardSales,
			'totalCardSales' => NumberHelper::currency($this->cardSales->sum('net')),

			'ar' => $this->ar,
			'totalCustomerDiscount' => NumberHelper::currency($this->generateCustomerDiscount()),
			'totalAr' => NumberHelper::currency($this->ar->sum('net')),

		];
	}

	protected function gcRevalidation(): array
	{
		$records = $this->revalidation();
		if (!is_null($records)) {
			return [
				'totalRevalidationPayment' => NumberHelper::currency($records)
			];
		}
		return ['gcRevalidation' => 'No GC Revalidation Transaction'];
	}
	protected function refund()
	{
		$refunds = $this->fundsRecords();
		$serviceCharge = $this->serviceCharge();
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

	protected function footer()
	{
		$this->dispatchProgress(ReportHelper::GENERATING_FOOTER_DATA);
		$discount = $this->generateTotalTransDiscount();
		$grandTotal = ReportHelper::grandTotal($this->cashSales, $this->cardSales, $this->ar, $discount);

		return [
			'totalTransactionDiscount' => NumberHelper::currency($discount),
			'grandTotalNet' => NumberHelper::currency($grandTotal),
		];
	}


}