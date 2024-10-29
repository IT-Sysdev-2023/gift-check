<?php

namespace App\Services\Treasury\Reports;
use App\Helpers\NumberHelper;
use App\Models\Denomination;
use App\Models\TransactionSale;
use App\Models\TransactionStore;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Benchmark;
use Illuminate\Http\Request;
use App\Models\Store;
use Illuminate\Support\Facades\DB;


class ReportsHandler
{
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

	protected function dataForPdf(Request $request)
	{
		if (in_array('gcSales', $request->reportType)) {
			$denom = TransactionSale::selectRaw("
			COUNT(sales_transaction_id) AS cnt,
			IFNULL(SUM(denomination.denomination), 0) AS densum
			
		")
				->join('denomination', 'denom_id', '=', 'sales_denomination')
				->join('transaction_stores', 'trans_sid', 'sales_transaction_id')
				->when(
					$this->isDateRange($request),
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
				->where([['trans_store', $request->store], ['trans_type', 1]])
				->groupBy('denomination')
				->get();

			dd($denom);
			return $denom->transform(function ($record) {
				$record->cnt = NumberHelper::currency($record->cnt);
				$record->densum = NumberHelper::currency($record->densum);
				$record->denomination = NumberHelper::currency($record->denomination);
				return $record;
			});

		}
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