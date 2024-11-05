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
    public static function grandTotal($cashSales, $cardSales, $ar, $discount)
	{
		$cashSales = (string) $cashSales->sum('net');
		$cardSales = (string) $cardSales->sum('net');

		$ar = (string) $ar->sum('net');

		$totalSales = bcadd(bcadd($cashSales, $cardSales, 2), $ar, 2);

		return bcsub($totalSales, $discount, 2);
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
}