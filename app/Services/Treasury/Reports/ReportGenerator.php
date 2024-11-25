<?php

namespace App\Services\Treasury\Reports;
use App\Events\TreasuryReportEvent;
use App\Helpers\NumberHelper;
use App\Models\InstitutEod;
use App\Models\StoreEod;
use App\Models\TransactionPayment;
use App\Models\TransactionRefund;
use App\Models\TransactionRefundDetail;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\Date;
use App\Models\TransactionStore;
use App\Models\TransactionLinediscount;
use App\Models\TransactionSale;
use App\Models\Store;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\LazyCollection;
class ReportGenerator
{
	protected $headerProgress;
	protected $progress;
	protected $transactionDate;
	protected bool $isDateRange;


	protected $store;
	protected $reportId;

	public function __construct()
	{
		$this->reportId = now()->toImmutable()->toISOString();
		$this->progress = [
			'store' => '',
			'name' => '',
			'progress' => [
				'currentRow' => 0,
				'totalRow' => 0,
			],
			'info' => []
		];
	}
	public function dispatchProgress($descrip, $user)
	{
		$this->progress['store'] = ReportHelper::storeName($this->store);
		$this->progress['info'] = $descrip;
		$this->progress['name'] = 'Gc Report';
		TreasuryReportEvent::dispatch($user, $this->progress, $this->reportId);
	}
	public function dispatchProgressEod($descrip, $user)
	{
		$this->progress['info'] = $descrip;
		$this->progress['name'] = 'Eod Report';

		TreasuryReportEvent::dispatch($user, $this->progress, $this->reportId);
	}
	protected function setStore($store)
	{
		$this->store = $store;

		return $this;
	}
	// protected function setReportId()
	// {
	// 	$this->reportId = now()->toImmutable()->toISOString();

	// 	return $this;
	// }


	protected function setDateOfTransactionsEod( $request)
	{
		$this->isDateRange = in_array($request->transactionDate, ['dateRange', 'thisWeek', 'currentMonth', 'allTransactions']);

		$date = match ($request->transactionDate) {
			'today' => now(),
			'yesterday' => Date::yesterday(),
			'dateRange' => [$request->date[0], $request->date[1]],
			'thisWeek' => [now()->startOfWeek(), now()->endOfWeek()],
			'currentMonth' => [now()->startOfMonth(), now()->endOfMonth()],
			'allTransactions' => ReportHelper::allTransactionDateEod(),
			default => null
		};

		$this->transactionDate = $date;

		return $this;
	}
	protected function setDateOfTransactions($request)
	{
		$this->isDateRange = in_array($request->transactionDate, ['dateRange', 'thisWeek', 'currentMonth', 'allTransactions']);

		$date = match ($request->transactionDate) {
			'today' => now(),
			'yesterday' => Date::yesterday(),
			'dateRange' => [$request->date[0], $request->date[1]],
			'thisWeek' => [now()->startOfWeek(), now()->endOfWeek()],
			'currentMonth' => [now()->startOfMonth(), now()->endOfMonth()],
			'allTransactions' => ReportHelper::allTransactionDate($request->reportType, $this->store),
			default => null
		};

		$this->transactionDate = $date;

		return $this;
	}
	protected function pdfHeaderDate($request, User $user)
	{
		$this->dispatchProgress(ReportHelper::GENERATING_HEADER, $user);
		$store = Store::where('store_id', $this->store)->value('store_name');
		
		$header = collect([
			'reportCreated' => now()->toFormattedDateString(),
			'store' => $store,
			'reportType' => $request->reportType
		]);

		$transDateHeader = ReportHelper::transactionDateLabel($this->isDateRange, $this->transactionDate);

		$header->put('transactionDate', $transDateHeader);


		return $header;
	}

	protected function pdfEodHeaderDate()
	{

		$header = collect([
			'reportCreated' => now()->toFormattedDateString(),
		]);

		$transDateHeader = ReportHelper::transactionDateLabel($this->isDateRange, $this->transactionDate);

		$header->put('transactionDate', $transDateHeader);

		return $header;
	}
	protected function generateSalesData(int $type): LazyCollection
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
				fn(Builder $q) => $q->whereBetween('trans_datetime', $this->transactionDate),
				fn(Builder $q) => $q->whereDate('trans_datetime', $this->transactionDate)
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
	protected function generateCustomerDiscount()
	{


		return TransactionStore::selectRaw("COALESCE(SUM(transaction_payment.payment_internal_discount), 0) AS customerDiscount")
			->join('transaction_payment', 'transaction_payment.payment_trans_num', '=', 'transaction_stores.trans_sid')
			->where([['transaction_stores.trans_type', '3'], ['transaction_stores.trans_store', $this->store]])
			->when(
				$this->isDateRange,
				fn(Builder $q) => $q->whereBetween('trans_datetime', $this->transactionDate),
				fn(Builder $q) => $q->whereDate('trans_datetime', $this->transactionDate)
			)->value('customerDiscount');
	}
	protected function generateTotalTransDiscount()
	{
		return TransactionStore::selectRaw("COALESCE(SUM(transaction_docdiscount.trdocdisc_amnt),0) as total")
			->join('transaction_docdiscount', 'transaction_docdiscount.trdocdisc_trid', '=', 'transaction_stores.trans_sid')
			->where('transaction_stores.trans_store', $this->store)
			->when(
				$this->isDateRange,
				fn(Builder $q) => $q->whereBetween('trans_datetime', $this->transactionDate),
				fn(Builder $q) => $q->whereDate('trans_datetime', $this->transactionDate)
			)->value('total');
	}

	protected function hasRecords($request, User $user): bool
	{
		$this->dispatchProgress(ReportHelper::CHECKING_RECORDS, $user);
		return TransactionStore::whereHas('ledgerStore')
			->where('trans_store', $this->store)
			->when((in_array('gcSales', $request->reportType)) ?? null, function ($q) use ($request) {
				$q->whereIn('trans_type', ['1', '2', '3'])
					->when(
						(in_array('gcRevalidation', $request->reportType)) ?? null,
						fn(Builder $true) => $true->orWhere('trans_type', '6'),
						fn(Builder $false) => $false->where('trans_type', '6')
					)
					->when(
						(in_array('refund', $request->reportType)) ?? null,
						fn(Builder $true) => $true->orWhere('trans_type', '5'),
						fn(Builder $false) => $false->where('trans_type', '5')
					);
			})
			->when(
				$this->isDateRange,
				fn(Builder $q) => $q->whereBetween('trans_datetime', $this->transactionDate),
				fn(Builder $q) => $q->whereDate('trans_datetime', $this->transactionDate)
			)
			->exists();
	}

	protected function hasEodRecords(User $user)
	{
		// $this->dispatchProgressEod(ReportHelper::CHECKING_RECORDS, $user);
		if ($this->isDateRange) {
			$query = InstitutEod::whereBetween('ieod_date', $this->transactionDate);
		} else {
			$query = InstitutEod::whereDate('ieod_date', $this->transactionDate);
		}
		return $query->exists();
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
				fn(Builder $q): Builder => $q->where('trans_store', $this->store)->when(
					$this->isDateRange,
					fn(Builder $q) => $q->whereBetween('trans_datetime', $this->transactionDate),
					fn(Builder $q) => $q->whereDate('trans_datetime', $this->transactionDate)
				)
			)
			->withSum('denomination', 'denomination')
			->groupBy('refund_trans_id', 'refund_denom')
			->first();
	}

	protected function serviceCharge()
	{
		return TransactionRefundDetail::selectRaw("trefundd_trstoresid, COALESCE(SUM(transaction_refund_details.trefundd_servicecharge),0) as scharge")->whereHas(
			'transactionStore',
			fn(Builder $q) => $q->where('trans_store', $this->store)->when(
				$this->isDateRange,
				fn(Builder $q) => $q->whereBetween('trans_datetime', $this->transactionDate),
				fn(Builder $q) => $q->whereDate('trans_datetime', $this->transactionDate)
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
					fn(Builder $q) => $q->whereBetween('trans_datetime', $this->transactionDate),
					fn(Builder $q) => $q->whereDate('trans_datetime', $this->transactionDate)
				)
			)->value('reval');
	}
}