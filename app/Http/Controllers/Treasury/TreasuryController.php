<?php

namespace App\Http\Controllers\Treasury;

use App\DashboardClass;
use App\Helpers\NumberHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\ApprovedGcRequestResource;
use App\Http\Resources\BudgetLedgerResource;
use App\Http\Resources\BudgetRequestResource;
use App\Http\Resources\DenominationResource;
use App\Http\Resources\GcLedgerResource;
use App\Http\Resources\ProductionRequestResource;
use App\Http\Resources\SpecialExternalGcRequestResource;
use App\Http\Resources\StoreGcRequestResource;
use App\Models\BudgetRequest;
use App\Models\Denomination;
use App\Services\Treasury\RegularGcProcessService;
use Illuminate\Support\Facades\DB;
use App\Models\Gcbarcodegenerate;
use App\Models\LedgerBudget;
use App\Models\ProductionRequest;
use App\Models\ProductionRequestItem;
use App\Models\SpecialExternalCustomer;
use App\Models\SpecialExternalGcrequest;
use App\Models\SpecialExternalGcrequestEmpAssign;
use App\Services\Treasury\ColumnHelper;
use App\Services\Treasury\Dashboard\BudgetRequestService;
use App\Services\Treasury\Dashboard\GcProductionRequestService;
use App\Services\Treasury\Dashboard\StoreGcRequestService;
use App\Services\Treasury\LedgerService;
use App\Services\Treasury\Transactions\TransactionProductionRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TreasuryController extends Controller
{
    public function __construct(
        public DashboardClass $dashboardClass,
        public LedgerService $ledgerService,
        public BudgetRequestService $budgetRequestService,
        public StoreGcRequestService $storeGcRequestService,
        public GcProductionRequestService $gcProductionRequestService,
        public TransactionProductionRequest $transactionProductionRequest,
        public RegularGcProcessService $regularGcProcessService
    ) {
    }
    public function index()
    {
        $record = $this->dashboardClass->treasuryDashboard();

        return inertia('Treasury/TreasuryDashboard', ['data' => $record]);
    }
    public function budgetLedger(Request $request)
    {
        $record = $this->ledgerService->budgetLedger($request);

        return inertia('Treasury/Table', [
            'filters' => $request->all('search', 'date'),
            'remainingBudget' => LedgerBudget::currentBudget(),
            'data' => BudgetLedgerResource::collection($record),
            'columns' => \App\Helpers\ColumnHelper::$budget_ledger_columns,
        ]);
    }
    public function gcLedger(Request $request)
    {
        $record = $this->ledgerService->gcLedger($request);
        return inertia('Treasury/Table', [
            'filters' => $request->all('search', 'date'),
            'remainingBudget' => LedgerBudget::currentBudget(),
            'data' => GcLedgerResource::collection($record),
            'columns' => \App\Helpers\ColumnHelper::$gc_ledger_columns,
        ]);
    }

    //BUDGET REQUEST
    public function approvedRequest(Request $request)
    {
        $record = $this->budgetRequestService->approvedRequest($request);
        return inertia(
            'Treasury/Dashboard/TableApproved',
            [
                'filters' => $request->all('search', 'date'),
                'title' => 'Approved Budget Request',
                'data' => BudgetRequestResource::collection($record),
                'columns' => ColumnHelper::$approved_buget_request,
            ]

        );
    }
    public function viewApprovedRequest(BudgetRequest $id): JsonResponse
    {
        $data = $this->budgetRequestService->viewApprovedRequest($id);
        return response()->json($data);
    }
    public function pendingRequest()
    {
        $record = $this->budgetRequestService->pendingRequest();

        return inertia(
            'Treasury/Dashboard/PendingRequest',
            [
                'currentBudget' => LedgerBudget::currentBudget(),
                'title' => 'Update Budget Entry Form',
                'data' => $record,
            ]

        );
    }
    public function submitBudgetEntry(BudgetRequest $id, Request $request)
    {
        return $this->budgetRequestService->submitBudgetEntry($id, $request);
    }
    public function downloadDocument($file)
    {
        return $this->budgetRequestService->downloadDocument($file);
    }
    public function cancelledRequest(Request $request)
    {
        $record = $this->budgetRequestService->cancelledRequest($request);

        return inertia(
            'Treasury/Dashboard/TableApproved',
            [
                'filters' => $request->all('search', 'date'),
                'title' => 'Cancelled Budget Request',
                'data' => BudgetRequestResource::collection($record),
                'columns' => ColumnHelper::$cancelled_buget_request,
            ]

        );
    }
    public function viewCancelledRequest(BudgetRequest $id): JsonResponse
    {
        $record = $this->budgetRequestService->viewCancelledRequest($id);
        return response()->json($record);
    }

    //STORE GC
    public function pendingRequestStoreGc(Request $request)
    {
        $record = $this->storeGcRequestService->pendingRequest($request);

        return inertia(
            'Treasury/Dashboard/TableStoreGc',
            [
                'filters' => $request->all('search', 'date'),
                'title' => 'Pending Request',
                'data' => StoreGcRequestResource::collection($record),
                'columns' => ColumnHelper::$pendingStoreGcRequest,
            ]

        );
    }
    public function releasedGc(Request $request)
    {
        $record = $this->storeGcRequestService->releasedGc($request);

        return inertia(
            'Treasury/Dashboard/TableStoreGc',
            [
                'filters' => $request->all('search', 'date'),
                'title' => 'Store Released Gc',
                'data' => ApprovedGcRequestResource::collection($record),
                'columns' => ColumnHelper::$releasedStoreGcRequest,
            ]

        );
    }
    public function cancelledRequestStoreGc(Request $request)
    {
        $record = $this->storeGcRequestService->cancelledRequest($request);
        return inertia(
            'Treasury/Dashboard/TableStoreGc',
            [
                'filters' => $request->all('search', 'date'),
                'title' => 'Store Cancelled Request',
                'data' => StoreGcRequestResource::collection($record),
                'columns' => ColumnHelper::$cancelledStoreGcRequest,
            ]

        );
    }
    public function reprint($id)
    {
        $pdfContent = $this->storeGcRequestService->reprint($id);

        return response($pdfContent, 200)->header('Content-Type', 'application/pdf');
    }
    public function viewCancelledGc($id): JsonResponse
    {
        $record = $this->storeGcRequestService->viewCancelledGc($id);
        return response()->json($record);
    }

    //GC PRODUCTION REQUEST
    public function approvedProductionRequest(Request $request)
    {

        $record = $this->gcProductionRequestService->approvedRequest($request);

        return inertia(
            'Treasury/Dashboard/TableGcProduction',
            [
                'filters' => $request->only('search', 'date'),
                'title' => 'Approved GC Production Request',
                'data' => ProductionRequestResource::collection($record),
                'columns' => ColumnHelper::$approvedProductionRequest,
            ]

        );
    }
    public function viewApprovedProduction($id): JsonResponse
    {

        $record = $this->gcProductionRequestService->viewApprovedProduction($id);
        $data = [
            'total' => $record->totalRow,
            'productionRequest' => new ProductionRequestResource($record->productionRequest),
            'items' => $record->transformItems
        ];
        return response()->json($data);
    }
    public function viewBarcodeGenerate($id): JsonResponse
    {
        $record = $this->gcProductionRequestService->viewBarcodeGenerated($id);

        return response()->json($record);
    }
    public function viewRequisition($id): JsonResponse
    {
        $record = $this->gcProductionRequestService->viewRequisition($id);
        return response()->json($record);
    }

    //SPECIAL GC REQUEST
    public function pendingSpecialGc(Request $request)
    {
        $record = SpecialExternalGcrequest::with(
            'user:user_id,firstname,lastname',
            'specialExternalGcrequestItems:specit_trid,specit_denoms,specit_qty',
            'specialExternalCustomer:spcus_id,spcus_acctname,spcus_companyname'
        )
            ->select('spexgc_num', 'spexgc_dateneed', 'spexgc_id', 'spexgc_datereq', 'spexgc_company', 'spexgc_reqby')
            ->where([
                ['special_external_gcrequest.spexgc_status', 'pending'],
                ['special_external_gcrequest.spexgc_promo', '0']
            ])
            ->paginate()
            ->withQueryString();

        // dd(SpecialExternalGcRequestResource::collection($record)->toArray(request()));

        return inertia(
            'Treasury/Dashboard/SpecialGcTable',
            [
                'filters' => $request->only('search', 'date'),
                'title' => 'Special GC Request',
                'data' => SpecialExternalGcRequestResource::collection($record),
                'columns' => ColumnHelper::$pendingSpecialGc,
            ]
        );
    }
    public function updatePendingSpecialGc(SpecialExternalGcrequest $id)
    {
        $record = $id->load('specialExternalCustomer', 'specialExternalBankPaymentInfo', 'document', 'specialExternalGcrequestEmpAssign');

        // dd($record->toArray());
        // dd((new SpecialExternalGcRequestResource($record))->toArray(request()));
        return inertia(
            'Treasury/Dashboard/UpdateSpecialExternal',
            [
                'title' => 'Special GC Request',
                'data' => new SpecialExternalGcRequestResource($record),
                'options' => self::options()
            ]
        );
    }

    private function options()
    {
        return SpecialExternalCustomer::has('user')
            ->select('spcus_id as value', 'spcus_by', 'spcus_companyname as label', 'spcus_acctname as account_name')
            ->where('spcus_type', 2)
            ->orderByDesc('spcus_id')
            ->get();
    }

    public function getAssignEmployee(Request $request)
    {
        //in Development
        $record = SpecialExternalGcrequestEmpAssign::select(
            'spexgcemp_fname as fname',
            'spexgcemp_lname as lname',
            'spexgcemp_mname as mname',
            'spexgcemp_extname as xname'
        )->where('spexgcemp_trid', $request->id)->get();

        dd($request->id);
        return response()->json([
            'data' => $record,
            'columns' => [
                [
                    'title' => 'Last Name',
                    'dataIndex' => 'lname',
                ],
                [
                    'title' => 'First Name',
                    'dataIndex' => 'fname',
                ],
                [
                    'title' => 'Middle Name',
                    'dataIndex' => 'mname',
                ],
                [
                    'title' => 'Name Ext.',
                    'dataIndex' => 'xname',
                ],
            ]
        ]);
    }

    public function addAssignEmployee(Request $request)
    {
        dd($request->all());
    }

    //TRANSACTIONS

    public function budgetRequest(Request $request)
    {
        $br = BudgetRequest::max('br_no');

        return inertia('Treasury/Transactions/BudgetRequest', [
            'title' => 'Budget Request',
            'br' => NumberHelper::leadingZero($br + 1),
            'remainingBudget' => LedgerBudget::currentBudget(),

        ]);
    }

    public function budgetRequestSubmission(Request $request)
    {
        $r = BudgetRequest::whereRelation('user', 'usertype', $request->user()->usertype)
            ->where('br_request_status', 0)
            ->count();
        if ($r) {
            return redirect()->back()->with('error', 'You have pending budget request');
        }

        $request->validate([
            "br" => 'required',
            "dateNeeded" => 'required|date',
            "budget" => 'required|not_in:0',
            "remarks" => 'required',
            'file' => 'nullable|image|mimes:jpeg,png,jpg|max:5048'
        ]);
        $dept = userDepartment($request->user());

        $filename = $this->createFileName($request);
    }
    //Production Requests
    public function giftCheck()
    {

        $denomination = Denomination::select('denomination', 'denom_id')->where([['denom_type', 'RSGC'], ['denom_status', 'active']])->get();
        $latestRecord = ProductionRequest::max('pe_num');
        $increment = $latestRecord ? $latestRecord + 1 : 1;

        return inertia('Treasury/Transactions/GiftCheck', [
            'title' => 'Gift Check',
            'denomination' => DenominationResource::collection($denomination),
            'prNo' => NumberHelper::leadingZero($increment),
            'remainingBudget' => LedgerBudget::currentBudget(),
        ]);
    }
    public function giftCheckStore(Request $request)
    {
        $this->transactionProductionRequest->storeGc($request);
    }
    public function acceptProductionRequest(Request $request, $id)
    {
        $this->regularGcProcessService->approveProductionRequest($request, $id);
        return redirect()->back()->with('success', 'Successfully Processed!');
    }


}
