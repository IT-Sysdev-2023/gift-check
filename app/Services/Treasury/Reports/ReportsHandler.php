<?php

namespace App\Services\Treasury\Reports;

use App\Events\TreasuryReportEvent;
use App\Helpers\NumberHelper;
use App\Models\InstitutEod;
use App\Models\StoreEod;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;

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

	protected function eodRecords()
	{
		$this->dispatchProgressEod(2);
		$query = InstitutEod::select('ieod_by', 'ieod_id', 'ieod_num', 'ieod_date')
			->with('user:user_id,firstname,lastname');

		if ($this->isDateRange) {
			$query->whereBetween('ieod_date', $this->transactionDate);
		} else {
			$query->whereDate('ieod_date', $this->transactionDate);
		}

		$percentage = 1;

		$this->progress['progress']['totalRow'] = $query->count();

		return $query->cursor()->map(function ($item) use (&$percentage) {
			//Dispatch
			$this->progress['progress']['currentRow'] = $percentage++;
			TreasuryReportEvent::dispatch(Auth::user(), $this->progress);

            $item->fullname = $item->user->fullname;
            $item->date = $item->ieod_date->toDayDateTimeString();
            return $item;
        });
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