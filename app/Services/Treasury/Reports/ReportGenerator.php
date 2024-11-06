<?php

namespace App\Services\Treasury\Reports;
use App\Helpers\NumberHelper;
use App\Models\TransactionPayment;
use App\Models\TransactionRefund;
use App\Models\TransactionRefundDetail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\Date;
use Illuminate\Http\Request;
use App\Models\TransactionStore;
use App\Models\TransactionLinediscount;
use App\Models\TransactionSale;
use App\Models\Store;
class ReportGenerator
{

	protected $transactionDate;
	protected bool $isDateRange;
	protected $store;
	protected function pdfHeaderDate(Request $request)
	{
		$store = Store::where('store_id', $this->store)->value('store_name');

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
			'allTransactions' => ReportHelper::setAllTransactionDate($this->allTransaction($request))
		};

		$header->put('transactionDate', $transDateHeader);

		return $header;
	}
	protected function generateSalesData(Request $request, int $type)
	{
		$transactionLines = TransactionLinediscount::select('gc.denom_id as denom', DB::raw('SUM(trlinedis_discamt) as discount'))
			->join('gc', 'gc.barcode_no', '=', 'transaction_linediscount.trlinedis_barcode')
			->groupBy('gc.denom_id');

		$denom = TransactionSale::selectRaw("
					COUNT(sales_transaction_id) AS cnt,
					COALESCE(SUM(denomination.denomination), 0) AS densum,
					COALESCE(line.discount, 0) AS lineDiscount,
					denomination.denomination,
					(COALESCE(SUM(denomination.denomination), 0) - COALESCE(line.discount, 0)) AS net
			")
			->join('denomination', 'denom_id', '=', 'sales_denomination')
			->join('transaction_stores', 'trans_sid', 'sales_transaction_id')
			->leftJoinSub($transactionLines, 'line', function (JoinClause $join) {
				$join->on('transaction_sales.sales_denomination', '=', 'line.denom');
			})
			->when(
				$this->isDateRange,
				fn($q) => $q->whereBetween('trans_datetime', $this->transactionDate),
				fn($q) => $q->whereDate('trans_datetime', $this->transactionDate)
			)
			->where([['trans_store', $this->store], ['trans_type', $type]])
			->groupBy('denomination', 'line.discount')
			->cursor();

		return $denom->map(function ($record) {
			$record->densum = NumberHelper::currency($record->densum);
			$record->denomination = NumberHelper::currency($record->denomination);
			$record->netIncome = NumberHelper::currency($record->net);
			return $record;
		});
	}
	protected function generateCustomerDiscount(Request $request)
	{
		return TransactionStore::selectRaw("COALESCE(SUM(transaction_payment.payment_internal_discount), 0) AS customerDiscount")
			->join('transaction_payment', 'transaction_payment.payment_trans_num', '=', 'transaction_stores.trans_sid')
			->where([['transaction_stores.trans_type', '3'], ['transaction_stores.trans_store', $this->store]])
			->when(
				$this->isDateRange,
				fn($q) => $q->whereBetween('trans_datetime', $this->transactionDate),
				fn($q) => $q->whereDate('trans_datetime', $this->transactionDate)
			)->value('customerDiscount');
	}
	protected function generateTotalTransDiscount()
	{
		return TransactionStore::selectRaw("COALESCE(SUM(transaction_docdiscount.trdocdisc_amnt),0) as total")
			->join('transaction_docdiscount', 'transaction_docdiscount.trdocdisc_trid', '=', 'transaction_stores.trans_sid')
			->where('transaction_stores.trans_store', $this->store)
			->when(
				$this->isDateRange,
				fn($q) => $q->whereBetween('trans_datetime', $this->transactionDate),
				fn($q) => $q->whereDate('trans_datetime', $this->transactionDate)
			)->value('total');
	}
	protected function setDateOfTransactions(Request $request)
	{
		$this->isDateRange = in_array($request->transactionDate, ['dateRange', 'thisWeek', 'currentMonth', 'allTransactions']);

		$date = match ($request->transactionDate) {
			'today' => now(),
			'yesterday' => Date::yesterday(),
			'dateRange' => [$request->date[0], $request->date[1]],
			'thisWeek' => [now()->startOfWeek(), now()->endOfWeek()],
			'currentMonth' => [now()->startOfMonth(), now()->endOfMonth()],
			'allTransactions' => $this->allTransaction($request),
			default => null
		};

		$this->transactionDate = $date;

		return $this;
	}
	protected function hasRecords(Request $request)
	{
		return TransactionStore::whereHas('ledgerStore')
			->where('trans_store', $this->store)
			->when((in_array('gcSales', $request->reportType)) ?? null, function ($q) use ($request) {
				$q->whereIn('trans_type', ['1', '2', '3'])
					->when(
						(in_array('gcRevalidation', $request->reportType)) ?? null,
						fn($true) => $true->orWhere('trans_type', '6'),
						fn($false) => $false->where('trans_type', '6')
					)
					->when(
						(in_array('refund', $request->reportType)) ?? null,
						fn($true) => $true->orWhere('trans_type', '5'),
						fn($false) => $false->where('trans_type', '5')
					);
			})
			->when(
				$this->isDateRange,
				fn($q) => $q->whereBetween('trans_datetime', $this->transactionDate),
				fn($q) => $q->whereDate('trans_datetime', $this->transactionDate)
			)
			->exists();
	}

	protected function fundsRecords()
	{
		return TransactionRefund::selectRaw(
			"refund_trans_id, 
			refund_denom, 
			COALESCE(SUM(transaction_refund.refund_linedisc),0) as lindisc, 
			COALESCE(SUM(transaction_refund.refund_sdisc),0) as trdisc
			"
		)
			->whereHas(
				'transactionStore',
				fn(Builder $q) => $q->where('trans_store', $this->store)->when(
					$this->isDateRange,
					fn($q) => $q->whereBetween('trans_datetime', $this->transactionDate),
					fn($q) => $q->whereDate('trans_datetime', $this->transactionDate)
				)
			)
			->withSum('denomination', 'denomination')
			->groupBy('refund_trans_id', 'refund_denom')
			->first();
	}

	protected function serviceCharge(Request $request)
	{
		return TransactionRefundDetail::selectRaw("trefundd_trstoresid, COALESCE(SUM(transaction_refund_details.trefundd_servicecharge),0) as scharge")->whereHas(
			'transactionStore',
			fn(Builder $q) => $q->where('trans_store', $this->store)->when(
				$this->isDateRange,
				fn($q) => $q->whereBetween('trans_datetime', $this->transactionDate),
				fn($q) => $q->whereDate('trans_datetime', $this->transactionDate)
			)
		)->groupBy('trefundd_trstoresid')->first();
	}

	protected function revalidation()
	{
		return TransactionPayment::selectRaw("COALESCE(SUM(transaction_payment.payment_amountdue),0) as reval")
			->whereHas(
				'transactionStore',
				fn(Builder $q) => $q->where('trans_store', $this->store)->when(
					$this->isDateRange,
					fn($q) => $q->whereBetween('trans_datetime', $this->transactionDate),
					fn($q) => $q->whereDate('trans_datetime', $this->transactionDate)
				)
			)->value('reval');
	}
	public function allTransaction(Request $request)
    {
        $transactions = TransactionStore::where('trans_store', $this->store)
            ->when((in_array('gcSales', $request->reportType)) ?? null, function ($q) use ($request) {
                $q->whereIn('trans_type', ['1', '2', '3'])
                    ->when(
                        (in_array('gcRevalidation', $request->reportType)) ?? null,
                        fn($true) => $true->orWhere('trans_type', '6'),
                        fn($false) => $false->where('trans_type', '6')
                    )
                    ->when(
                        (in_array('refund', $request->reportType)) ?? null,
                        fn($true) => $true->orWhere('trans_type', '4'),
                        fn($false) => $false->where('trans_type', '4')
                    );
            })
            ->selectRaw('MIN(trans_datetime) as start, MAX(trans_datetime) as end')->first();

        return !is_null($transactions->start) ? [$transactions->start, $transactions->end] : null;
    }
	
}