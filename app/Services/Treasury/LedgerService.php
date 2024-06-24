<?php

namespace App\Services\Treasury;

use App\Events\ApprovedReleasedEvents;
use App\Helpers\Excel\ExcelWriter;
use App\Helpers\ColumnHelper;
use App\Helpers\NumberHelper;
use App\Http\Resources\BudgetLedgerApprovedResource;
use App\Http\Resources\BudgetLedgerResource;
use App\Http\Resources\BudgetRequestResource;
use App\Http\Resources\GcLedgerResource;
use App\Models\BudgetRequest;
use App\Models\LedgerBudget;
use App\Models\LedgerCheck;
use App\Models\LedgerSpgc;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Services\Treasury\Dashboard\excel\ExtendsExcelService;
use Faker\Core\Number;
use Illuminate\Support\Facades\Auth;

class LedgerService extends ExcelWriter
{
    protected $record;
    protected $border;
    public function __construct()
    {
        parent::__construct();
    }
    public function budgetLedger(Request $request) //ledger_budget.php
    {
        $record =  LedgerBudget::with('approvedGcRequest.storeGcRequest.store:store_id,store_name')
            ->filter($request->only('search', 'date'))
            ->select(
                [
                    'bledger_id',
                    'bledger_no',
                    'bledger_trid',
                    'bledger_datetime',
                    'bledger_type',
                    'bdebit_amt',
                    'bcredit_amt'
                ]
            )
            ->orderByDesc('bledger_no')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Treasury/Table', [
            'filters' => $request->all('search', 'date'),
            'remainingBudget' => LedgerBudget::currentBudget(),
            'data' => BudgetLedgerResource::collection($record),
            'columns' => ColumnHelper::$budget_ledger_columns,
        ]);
    }

    public function viewBudgetRequestApproved(BudgetRequest $budgetRequest)
    {
        $record = $budgetRequest->load(['user:user_id,firstname,lastname', 'approvedBudgetRequest.user:user_id,firstname,lastname']);
        $data = new BudgetRequestResource($record);
        return response()->json($data);
    }

    public function budgetRequestApproved(Request $request)
    {
        $record = BudgetRequest::join('users', 'users.user_id', 'budget_request.br_requested_by')
            ->leftJoin('approved_budget_request', 'budget_request.br_id', 'approved_budget_request.abr_budget_request_id')
            ->select('users.lastname', 'users.firstname', 'br_request', 'br_no', 'br_id', 'br_requested_at', 'abr_approved_by', 'abr_approved_at')
            ->filter($request->only('search', 'date'))
            ->where('br_request_status', '1')
            ->orderByDesc('br_requested_at')
            ->paginate()
            ->withQueryString();

        return inertia(
            'Treasury/Table',
            [
                'filters' => $request->all('search', 'date'),
                'title' => 'Approved Budget Request',
                'data' => BudgetLedgerApprovedResource::collection($record),
                'columns' => ColumnHelper::$approved_buget_request,
            ]

        );
    }

    public function gcLedger(Request $request) // gccheckledger.php
    {

        $record = LedgerCheck::with('user:user_id,firstname,lastname')
            ->select(
                'cledger_id',
                'c_posted_by',
                'cledger_no',
                'cledger_datetime',
                'cledger_type',
                'cledger_desc',
                'cdebit_amt',
                'ccredit_amt',
                'c_posted_by'
            )
            ->orderBy('cledger_id')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Treasury/Table', [
            'filters' => $request->all('search', 'date'),
            'remainingBudget' => LedgerBudget::currentBudget(),
            'data' => GcLedgerResource::collection($record),
            'columns' => ColumnHelper::$gc_ledger_columns,
        ]);
    }
    public static function spgcLedger($filters)
    {
        return LedgerSpgc::select(
            'spgcledger_id',
            'spgcledger_no',
            'spgcledger_trid',
            'spgcledger_datetime',
            'spgcledger_type',
            'spgcledger_debit',
            'spgcledger_credit'
        )->filter($filters)
            ->paginate(10)->withQueryString();
    }

}
