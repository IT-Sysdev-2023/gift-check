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

    public static function setAllTransactionDate(array | null $date)
    {
        return is_null($date) ? 'No Transactions' : Date::parse($date[0])->toFormattedDateString() . ' - ' . Date::parse($date[1])->toFormattedDateString();
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