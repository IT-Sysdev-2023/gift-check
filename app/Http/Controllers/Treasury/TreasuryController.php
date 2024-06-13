<?php

namespace App\Http\Controllers\Treasury;

use App\DashboardClass;
use App\Helpers\ColumnHelper;
use App\Http\Controllers\Controller;
use App\Models\BudgetRequest;
use App\Services\Treasury\Dashboard\BudgetRequestService;
use App\Services\Treasury\LedgerService;
use Illuminate\Http\Request;

class TreasuryController extends Controller
{
    public function __construct(public DashboardClass $dashboardClass, public LedgerService $ledgerService)
    {
    }

    public function index()
    {
        $record = $this->dashboardClass->treasuryDashboard();
        return inertia('Treasury/Dashboard', ['data' => $record]);
    }
    public function budgetLedger(Request $request)
    {
        return $this->ledgerService->budgetLedger($request);
    }

    public function budgetLedgerApproved()
    {
        //     $table = 'budget_request';
        // $select = "budget_request.br_id,
        //     budget_request.br_request,
        //     budget_request.br_requested_at,
        //     budget_request.br_no,
        //     CONCAT(brequest.firstname,' ',brequest.lastname) as breq,
        //     approved_budget_request.abr_approved_by,
        //     approved_budget_request.abr_approved_at";
        // $where = "br_request_status = '1'";
        // $join = 'INNER JOIN
        //         users as brequest
        //     ON
        //         brequest.user_id = budget_request.br_requested_by
        //     LEFT JOIN
        //         approved_budget_request
        //     ON
        //         approved_budget_request.abr_budget_request_id  = budget_request.br_id';
        // $limit = '';
        // $data = getAllData($link,$table,$select,$where,$join,$limit);

        $record = BudgetRequest::with('user')->leftJoin('approved_budget_request', 'budget_request.br_id', '=', 'approved_budget_request.abr_budget_request_id')
            ->select('br_request', 'br_no', 'br_id', 'br_requested_at', 'abr_approved_by', 'abr_approved_at')
            ->where('br_request_status', '1')->get();
        dd($record);
        return inertia(
            'Ledger',
            [
                'title' => 'Approved Budget Request',
                'data' => $record,
                'columns' => ColumnHelper::$approved_buget_request,
            ]

        );
    }

    public function gcLedger(Request $request)
    {
        return $this->ledgerService->gcLedger($request);
    }
}
