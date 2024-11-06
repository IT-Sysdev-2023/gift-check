<?php

namespace App\Services\Treasury\Reports;

use Illuminate\Support\Facades\Date;
use App\Models\TransactionStore;

class ReportHelper
{
    public static function grandTotal($cashSales, $cardSales, $ar, $discount)
    {
        $cashSales = (string) $cashSales->sum('net');
        $cardSales = (string) $cardSales->sum('net');

        $ar = (string) $ar->sum('net');

        $totalSales = bcadd(bcadd($cashSales, $cardSales, 2), $ar, 2);

        return bcsub($totalSales, $discount, 2);
    }

    public static function transactionDateLabel(bool $isRange, string|array $date)
    {
        if ($isRange) {
            $from = Date::parse($date[0])->toFormattedDateString();
            $to = Date::parse($date[1])->toFormattedDateString();

            return is_null($date[0]) ? 'No Transactions' : "{$from} to {$to}";
        }
        return Date::parse($date)->toFormattedDateString();
    }

    public static function allTransactionDate($reportType, $store)
    {
        $transactions = TransactionStore::where('trans_store', $store)
            ->when((in_array('gcSales', $reportType)) ?? null, function ($q) use ($reportType) {
                $q->whereIn('trans_type', ['1', '2', '3'])
                    ->when(
                        (in_array('gcRevalidation', $reportType)) ?? null,
                        fn($true) => $true->orWhere('trans_type', '6'),
                        fn($false) => $false->where('trans_type', '6')
                    )
                    ->when(
                        (in_array('refund', $reportType)) ?? null,
                        fn($true) => $true->orWhere('trans_type', '4'),
                        fn($false) => $false->where('trans_type', '4')
                    );
            })
            ->selectRaw('MIN(trans_datetime) as start, MAX(trans_datetime) as end')->first();

        return !is_null($transactions->start) ? [$transactions->start, $transactions->end] : null;
    }
}