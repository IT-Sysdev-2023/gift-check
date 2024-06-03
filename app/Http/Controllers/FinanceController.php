<?php

namespace App\Http\Controllers;

use App\Helpers\ColumnHelper;
use App\Models\LedgerBudget;
use App\Services\Treasury\LedgerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Inertia\Inertia;

class FinanceController extends Controller
{
    public function budgetLedger()
    {

        $data = LedgerService::budgetLedger();

        $data->transform(function ($item) {

            $item->bledger_datetime = Date::parse($item->bledger_datetime)->toFormattedDateString();
            
            return $item;
        });

        return Inertia::render('Finance/BudgetLedger', [
            'data' => $data,
            'columns' => ColumnHelper::$budget_ledger_columns,
        ]);
    }
}
