<?php

namespace App\Services\Treasury\Reports;

use App\Helpers\NumberHelper;
use App\Models\TransactionRefund;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\Date;
use Illuminate\Http\Request;
use App\Models\TransactionStore;
use App\Models\TransactionLinediscount;
use App\Models\TransactionSale;
class ReportHelper
{
    public function isRefundExist(Request $request){
        $data = TransactionRefund::join('denomination', 'denomination.denom_id', '=', 'transaction_refund.refund_denom')
        ->join('transaction_store', 'transaction_stores.trans_sid', '=', 'transaction_refund.refund_trans_id')
        ->where('transaction_stores.trans_store', $request->store)
        ->get();
    }
    public static function grandTotal($cashSales, $cardSales, $ar, $discount)
	{
		$cashSales = (string) $cashSales->sum('net');
		$cardSales = (string) $cardSales->sum('net');

		$ar = (string) $ar->sum('net');

		$totalSales = bcadd(bcadd($cashSales, $cardSales, 2), $ar, 2);

		return bcsub($totalSales, $discount, 2);
	}
    public static function transactionsDate(Request $request): array|null
    {
        $res = match ($request->transactionDate) {
            'dateRange' => [$request->date[0], $request->date[1]],
            'thisWeek' => [now()->startOfWeek(), now()->endOfWeek()],
            'currentMonth' => [now()->startOfMonth(), now()->endOfMonth()],
            'allTransactions' => self::allTransaction($request)
        };
        return $res;
    }

    public static function allTransaction(Request $request)
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
    public static function extractDateRange(Request $request)
    {
        if ($request->transactionDate === 'dateRange') {
            $from = Date::parse($request->date[0])->toFormattedDateString();
            $to = Date::parse($request->date[1])->toFormattedDateString();
        }
       
        return (object) [
            'from' => $from ?? null,
            'to' => $to ?? null
        ];
    }

    public static function isDateRange($request)
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
    public static function transactionDateSingle(Request $request)
    {
        return match ($request->transactionDate) {
            'today' => now(),
            'yesterday' => Date::yesterday(),
            default => null
        };
    }
}