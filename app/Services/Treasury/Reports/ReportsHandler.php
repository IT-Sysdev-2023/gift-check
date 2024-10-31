<?php

namespace App\Services\Treasury\Reports;
use App\Helpers\NumberHelper;
use App\Models\Denomination;
use App\Models\TransactionLinediscount;
use App\Models\TransactionSale;
use App\Models\TransactionStore;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Benchmark;
use Illuminate\Http\Request;
use App\Models\Store;
use Illuminate\Support\Facades\DB;


class ReportsHandler
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
			'dateRange' => "{$this->extractDateRange($request)->from} to {$this->extractDateRange($request)->to}",
			'today' => now()->toFormattedDateString(),
			'yesterday' => Date::yesterday()->toFormattedDateString(),
			'thisWeek' => now()->startOfWeek()->toFormattedDateString() . ' to ' . now()->endOfWeek()->toFormattedDateString(),
			'currentMonth' => now()->startOfMonth()->toFormattedDateString() . ' to ' . now()->endOfMonth()->toFormattedDateString(),
			'allTransactions' => is_null($this->allTransaction($request))
			? 'No Transactions'
			: Date::parse($this->allTransaction($request)[0])->toFormattedDateString() .
			' - ' . Date::parse($this->allTransaction($request)[1])->toFormattedDateString()
		};

		$header->put('transactionDate', $transDateHeader);

		return $header;
	}
	protected function gcSales(Request $request)
	{
		return (object) [
			'cashSales' => $this->generateData($request, self::SALE_TYPE_CASH),
			'cardSales' => $this->generateData($request, self::SALE_TYPE_CARD),

			'ar' => $this->generateData($request, self::SALE_TYPE_CARD),
			'totalArCustomer' => $this->generateCustomerDiscount($request),

			'totalTransactionDiscount' => $this->generateTotalTransDiscount($request),
		];
	}

	private function generateData(Request $request, int $type)
	{

		if (in_array('gcSales', $request->reportType)) {

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
					$this->isDateRange($request),
					fn($q) => $q->whereBetween('trans_datetime', $this->transactionsDate($request)),
					fn($q) => $q->whereDate('trans_datetime', $this->transactionDateSingle($request))
				)
				->where([['trans_store', $request->store], ['trans_type', $type]])
				->groupBy('denomination', 'line.discount')
				->get();

			return $denom->transform(function ($record) {
				$record->densum = NumberHelper::currency($record->densum);
				$record->denomination = NumberHelper::currency($record->denomination);
				$record->netIncome = NumberHelper::currency($record->net);
				return $record;
			});

		}
	}
	private function generateCustomerDiscount(Request $request)
	{
		return TransactionStore::selectRaw("COALESCE(SUM(transaction_payment.payment_internal_discount), 0) AS customerDiscount")
			->join('transaction_payment', 'transaction_payment.payment_trans_num', '=', 'transaction_stores.trans_sid')
			->where([['transaction_stores.trans_type', '3'], ['transaction_stores.trans_store', $request->store]])
			->when(
				$this->isDateRange($request),
				fn($q) => $q->whereBetween('trans_datetime', $this->transactionsDate($request)),
				fn($q) => $q->whereDate('trans_datetime', $this->transactionDateSingle($request))
			)->value('customerDiscount');
	}
	private function generateTotalTransDiscount(Request $request)
	{
		return TransactionStore::selectRaw("COALESCE(SUM(transaction_docdiscount.trdocdisc_amnt),0) as total")
			->join('transaction_docdiscount', 'transaction_docdiscount.trdocdisc_trid', '=', 'transaction_stores.trans_sid')
			->where('transaction_stores.trans_store', $request->store)
			->when(
				$this->isDateRange($request),
				fn($q) => $q->whereBetween('trans_datetime', $this->transactionsDate($request)),
				fn($q) => $q->whereDate('trans_datetime', $this->transactionDateSingle($request))
			)->value('total');
	}
	protected function isExists(Request $request)
	{
		return TransactionStore::select('trans_sid', 'trans_number', 'trans_type', 'trans_datetime')->withWhereHas('ledgerStore')->where('trans_store', $request->store)
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
				$this->isDateRange($request) ?? null,
				fn($q): mixed => $q->whereBetween('trans_datetime', $this->transactionsDate($request)),
				function ($q) use ($request) {

					$date = match ($request->transactionDate) {
						'today' => now(),
						'yesterday' => Date::yesterday(),
						default => null
					};

					return $q->whereDate('trans_datetime', $date);
				}
			)
			->exists();
		// ->groupBy('trans_sid','trans_number','trans_type','trans_datetime')
		// ->orderBy('trans_sid')
		// ->limit(10)
		// ->get();
	}

	private function transactionsDate(Request $request): array|null
	{
		$res = match ($request->transactionDate) {
			'dateRange' => [$request->date[0], $request->date[1]],
			'thisWeek' => [now()->startOfWeek(), now()->endOfWeek()],
			'currentMonth' => [now()->startOfMonth(), now()->endOfMonth()],
			'allTransactions' => $this->allTransaction($request)
		};
		return $res;
	}
	public function transactionDateSingle(Request $request)
	{
		return match ($request->transactionDate) {
			'today' => now(),
			'yesterday' => Date::yesterday(),
			default => null
		};
	}

	private function isDateRange($request)
	{
		if (
			$request->transactionDate == 'dateRange'
			|| $request->transactionDate == 'thisWeek'
			|| $request->transactionDate == 'currentMonth'
			|| $request->transactionDate == 'allTransactions'
		) {
			return true;
		} else {
			return false;
		}

	}
	private function allTransaction(Request $request)
	{
		$transactions = TransactionStore::where('trans_store', $request->store)
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
	private function extractDateRange(Request $request)
	{
		if ($request->transactionDate === 'dateRange') {
			$from = Date::parse($request->date[0])->toFormattedDateString();
			$to = Date::parse($request->date[1])->toFormattedDateString();
		}
		return (object) [
			'from' => $from,
			'to' => $to
		];
	}
}