<?php

namespace App\Http\Controllers;

use App\DashboardClass;
use App\Helpers\ColumnHelper;
use App\Http\Requests\PromoForApprovalRequest;
use App\Http\Resources\BudgetLedgerResource;
use App\Http\Resources\SpgcLedgerResource;
use App\Models\ApprovedRequest;
use App\Models\Assignatory;
use App\Models\BudgetAdjustment;
use App\Models\BudgetRequest;
use App\Models\LedgerBudget;
use App\Models\LedgerSpgc;
use App\Models\SpecialExternalGcrequest;
use App\Models\SpecialExternalGcrequestEmpAssign;
use App\Models\SpecialExternalGcrequestItem;
use App\Models\User;
use App\Services\Finance\ApprovedPendingPromoGCRequestService;
use App\Services\Finance\ApprovedReleasedPdfExcelService;
use App\Services\Finance\ApprovedReleasedReportService;
use App\Services\Finance\FinanceService;
use App\Services\Finance\SpgcLedgerExcelService;
use App\Services\Finance\SpgcService;
use App\Services\Treasury\LedgerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

use function PHPUnit\Framework\isNull;

class FinanceController extends Controller
{

    public function __construct(
        public LedgerService $ledgerService,
        public ApprovedReleasedPdfExcelService $appRelPdfExcelService,
        public DashboardClass $dashboardClass,
        public FinanceService $financeService
    ) {
    }

    public function index()
    {
        return inertia('Finance/FinanceDashboard', [
            'count' => $this->dashboardClass->financeDashboard(),
        ]);
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

    public function spgcLedger(Request $request)
    {
        $data = LedgerService::spgcLedger($request);

        $operators = SpgcService::operatorsFn();

        return inertia('Finance/SpgcLedger', [
            'data' => SpgcLedgerResource::collection($data),
            'columns' => ColumnHelper::$budget_ledger_columns,
            'operators' => $operators,
            'filters' => $request->only([
                'search',
                'date'
            ])
        ]);
    }


    public function approvedAndReleasedSpgc(Request $request)
    {
        // dd($request->approvedType);
        $dataCus = ApprovedReleasedReportService::approvedReleasedQueryCus($request);
        $dataBar = ApprovedReleasedReportService::approvedReleasedQueryBar($request);



        return inertia('Finance/ApprovedAndReleaseSpgc', [
            'columns' => [
                'columnsCus' => ColumnHelper::$cus_table_columns,
                'columnsBar' => ColumnHelper::$bar_table_columns,
            ],
            'data' => [
                'dataCus' => $dataCus,
                'dataBar' => $dataBar
            ],
            'filters' => $request->only([
                'dateRange',
                'search',
                'key'
            ])
        ]);
    }
    public function approvedSpgdcPdfExcelFunction(Request $request)
    {
        if ($request->ext == 'pdf') {
            $dataCus = ApprovedReleasedReportService::approvedReleasedGenerate($request->all());
            $dataBar = ApprovedReleasedReportService::approvedReleasedBarGenerate($request->all());
            return $this->appRelPdfExcelService->approvedReleasedSpgcPdfWriteResult($request->dateRange, $dataCus, $dataBar, $request->approvedType);
        } elseif ($request->ext == 'excel') {
            $dataCus = ApprovedReleasedReportService::approvedReleasedGenerate($request->all());
            $dataBar = ApprovedReleasedReportService::approvedReleasedBarGenerate($request->all());
            return $this->appRelPdfExcelService->approvedReleasedSpgcExcelWriteResult($request->dateRange, $dataCus, $dataBar, $request->approvedType);
        }
    }
    public function releasedSpgcPdfExcelFunction(Request $request)
    {
        if ($request->ext == 'pdf') {
            $dataCus = ApprovedReleasedReportService::approvedReleasedGenerate($request->all());
            $dataBar = ApprovedReleasedReportService::approvedReleasedBarGenerate($request->all());
            return $this->appRelPdfExcelService->approvedReleasedSpgcPdfWriteResult($request->dateRange, $dataCus, $dataBar, $request->approvedType);
        } elseif ($request->ext == 'excel') {
            $dataCus = ApprovedReleasedReportService::approvedReleasedGenerate($request->all());
            $dataBar = ApprovedReleasedReportService::approvedReleasedBarGenerate($request->all());
            return $this->appRelPdfExcelService->approvedReleasedSpgcExcelWriteResult($request->dateRange, $dataCus, $dataBar, $request->approvedType);
        }
    }

    public function generateSpgcPromotionalExcel(Request $request)
    {

        $record = LedgerService::spgcLedgerToExcel($request);

        $save = (new SpgcLedgerExcelService())->record($record)->date($request->date)->writeResult()->save();

        return Inertia::render('Finance/Results/SpgcLedgerResult', [
            'filePath' => $save,
        ]);
    }
    public function pendingPromoRequest(Request $request)
    {
        return (new ApprovedPendingPromoGCRequestService())->pendingPromoGCRequestIndex($request);
    }

    public function approveRequest(PromoForApprovalRequest $request)
    {

        return (new ApprovedPendingPromoGCRequestService())->approveRequest($request);
    }

    public function approvedPromoRequest(Request $request)
    {
        return (new ApprovedPendingPromoGCRequestService())->approvedPromoGCRequestIndex($request);
    }


    public function specialGcPending(Request $request)
    {

        $gcType = $request->type;
        $external = SpecialExternalGcRequest::with('specialExternalGcrequestItemsHasMany')->join('users', 'users.user_id', '=', 'special_external_gcrequest.spexgc_reqby')
            ->join('special_external_customer', 'special_external_customer.spcus_id', '=', 'special_external_gcrequest.spexgc_company')
            ->where('special_external_gcrequest.spexgc_status', 'pending')
            ->where('spexgc_addemp', 'done')
            ->where('spexgc_promo', '0')
            ->get();

        $external->transform(function ($item) {
            $item->specialExternalGcrequestItemsHasMany->each(function ($subitem) {

                $subitem->subtotal = (float) $subitem->specit_denoms * (float) $subitem->specit_qty;
                return $subitem;
            });

            $item->total = $item->specialExternalGcrequestItemsHasMany->sum('subtotal');

            $item->fullname = ucwords($item->firstname . ' ' . $item->lastname);
            $item->dateRequeted = Date::parse($item->spexgc_datereq)->format('Y-F-d');
            $item->dateNeed = Date::parse($item->spexgc_dateneed)->format('Y-F-d');


            return $item;
        });


        $internal = SpecialExternalGcrequest::where('spexgc_status', 'pending')
            ->where('spexgc_addemp', 'done')
            ->where('spexgc_promo', '*')
            ->join('users', 'users.user_id', '=', 'special_external_gcrequest.spexgc_reqby')
            ->join('special_external_customer', 'special_external_customer.spcus_id', '=', 'special_external_gcrequest.spexgc_company')
            ->get();

        $internal->transform(function ($item) {

            $item->specialExternalGcrequestItemsHasMany->each(function ($subitem) {

                $subitem->subtotal = (float) $subitem->specit_denoms * (float) $subitem->specit_qty;
                return $subitem;
            });

            $item->total = $item->specialExternalGcrequestItemsHasMany->sum('subtotal');

            $item->fullname = ucwords($item->firstname . ' ' . $item->lastname);
            $item->dateRequeted = Date::parse($item->spexgc_datereq)->format('Y-F-d');
            $item->dateNeed = Date::parse($item->spexgc_dateneed)->format('Y-F-d');


            return $item;
        });

        $columns = array_map(
            fn($name, $field) => ColumnHelper::arrayHelper($name, $field),
            ['RFSEGC #', 'Date Requested', 'Date Needed', 'Total Denomination', 'Customer', 'Requested by', 'View'],
            ['spexgc_num', 'dateRequeted', 'dateNeed', 'total', 'spcus_acctname', 'fullname', 'View']
        );

        return Inertia::render('Finance/SpecialGcPending', [
            'external' => $external,
            'internal' => $internal,
            'columns' => ColumnHelper::getColumns($columns),
            'gctype' => $gcType
        ]);
    }

    public function SpecialGcApprovalForm(Request $request)
    {
        $gcType = $request->gcType;
        $id = $request->id;

        $gcHolder = SpecialExternalGcrequestEmpAssign::where('spexgcemp_trid', $id)->get();
        $gcHolder = $gcHolder->transform(function ($item) {
            $item->fullname = ucwords($item->spexgcemp_fname . ' ' . $item->spexgcemp_lname);
            return $item;
        });



        $columns = array_map(
            fn($name, $field) => ColumnHelper::arrayHelper($name, $field),
            ['Complete Name', 'Denomination'],
            ['fullname', 'spexgcemp_denom']
        );



        if ($gcType == 'external') {
            $currentBudget = LedgerBudget::where('bcus_guide', '!=', 'dti')
                ->selectRaw('SUM(bdebit_amt) as total_debit, SUM(bcredit_amt) as total_credit')
                ->first();
            $budget = $currentBudget->total_debit - $currentBudget->total_credit;
        } else {
            $currentBudget = LedgerSpgc::selectRaw('SUM(spgcledger_debit) as total_debit, SUM(spgcledger_credit) as total_credit')
                ->first();
            $budget = $currentBudget->total_debit - $currentBudget->total_credit;
        }

        $query = SpecialExternalGcRequest::join('users as preparedby', 'preparedby.user_id', '=', 'special_external_gcrequest.spexgc_reqby')
            ->join('users as checker', 'checker.user_id', '=', 'special_external_gcrequest.spexgc_addempaddby')
            ->join('special_external_customer', 'special_external_customer.spcus_id', '=', 'special_external_gcrequest.spexgc_company')
            ->join('access_page', 'access_page.access_no', '=', 'preparedby.usertype')
            ->where('special_external_gcrequest.spexgc_status', 'pending')
            ->where('special_external_gcrequest.spexgc_id', $id)
            ->select(
                'special_external_gcrequest.*',
                'preparedby.firstname as preparedby_firstname',
                'preparedby.lastname as preparedby_lastname',
                'checker.firstname as checker_first',
                'checker.lastname as checker_lastname',
                'special_external_customer.*',
                'access_page.*'
            )
            ->get();



        $query->transform(function ($item) {
            $item->specialExternalGcrequestItemsHasMany->each(function ($subitem) {

                $subitem->subtotal = (float) $subitem->specit_denoms * (float) $subitem->specit_qty;
                return $subitem;
            });
            $item->total = $item->specialExternalGcrequestItemsHasMany->sum('subtotal');

            $item->prepby = ucwords($item->preparedby_firstname . ' ' . $item->preparedby_lastname);
            $item->checkby = ucwords($item->checker_first . ' ' . $item->checker_lastname);
            $item->dateRequeted = Date::parse($item->spexgc_datereq)->format('Y-F-d');
            $item->dateNeed = Date::parse($item->spexgc_dateneed)->format('Y-F-d');


            return $item;
        });

        return Inertia::render('Finance/SpecialGcApprovalForm', [
            'data' => $query,
            'type' => $gcType,
            'currentBudget' => $budget,
            'gcHolder' => $gcHolder,
            'columns' => ColumnHelper::getColumns($columns),
        ]);
    }

    public function SpecialGcApprovalSubmit(Request $request)
    {
        $id = $request->formData['id'];
        $totalDenom = $request->data[0]['total'];
        $reqType = SpecialExternalGcrequest::select('spexgc_type')->where('spexgc_id', $id)->first();
        $currentbudget = LedgerBudget::whereNull('bledger_category')->get();
        $debit = $currentbudget->sum('bdebit_amt');
        $credit = $currentbudget->sum('bcredit_amt');
        $customer = SpecialExternalGcrequest::select('spexgc_company')->where('spexgc_id', $id)->get();
        $ledgerBudgetNum = LedgerBudget::select('bledger_no')->orderByDesc('bledger_id')->first();
        $nextLedgerBudgetNum = (int) $ledgerBudgetNum->bledger_no + 1;
        $pending = SpecialExternalGcrequest::where('spexgc_id', $id)->where('spexgc_status', 'pending')->exists();
        $specGet = SpecialExternalGcrequestEmpAssign::where('spexgcemp_barcode', '!=', '0')->orderByDesc('spexgcemp_id')->first()->spexgcemp_barcode + 1;

        if ($pending) {
            $cust = ($customer[0]->spexgc_company == 342 || $customer[0]->spexgc_company == 341) ? 'dti' : '';

            if ($request->formData['status'] == '1') {
                if ($totalDenom > ($debit - $credit)) {
                    return back()->with([
                        'type' => 'error',
                        'msg' => 'Opps!',
                        'description' => 'Total Denomination requested is bigger than current budget'
                    ]);
                } elseif (empty($request->formData['approveRemarks']) || empty($request->formData['checkedBy']) || empty($request->formData['approvedBy'])) {
                    return back()->with([
                        'type' => 'error',
                        'msg' => 'Opps!',
                        'description' => 'Please fill all required Fields'
                    ]);
                } else {
                    DB::transaction(function () use ($specGet, $reqType, $id, $request, $nextLedgerBudgetNum, $cust, $totalDenom) {
                        SpecialExternalGcrequest::where('spexgc_id', $id)
                            ->where('spexgc_status', 'pending')
                            ->update([
                                'spexgc_status' => 'approved'
                            ]);

                        ApprovedRequest::create([
                            'reqap_trid' => $id,
                            'reqap_approvedtype' => 'Special External GC Approved',
                            'reqap_remarks' => $request->formData['approveRemarks'],
                            'reqap_checkedby' => $request->formData['checkedBy'],
                            'reqap_approvedby' => $request->formData['approvedBy'],
                            'reqap_preparedby' => $request->user()->user_id,
                            'reqap_date' => now(),
                            'reqap_doc' => !is_null($request->file) ? $this->financeService->uploadFileHandler($request) : ''
                        ]);
                        LedgerBudget::create([
                            'bledger_no' => $nextLedgerBudgetNum,
                            'bledger_trid' => $id,
                            'bledger_datetime' => now(),
                            'bledger_type' => 'RFGCSEGC',
                            'bcus_guide' => $cust,
                            'bcredit_amt' => $totalDenom,
                            'bledger_category' => 'special'
                        ]);
                        if ($request['type'] === 'internal') {
                            LedgerSpgc::create([
                                'spgcledger_no' => $nextLedgerBudgetNum,
                                'spgcledger_trid' => $id,
                                'spgcledger_datetime' => now(),
                                'spgcledger_type' => 'RFGCSEGC',
                                'spgcledger_credit' => $totalDenom,
                                'spgcledger_typeid' => '0',
                                'spgcledger_group' => '0',
                                'spgcledger_debit' => '0',
                                'spgctag' => '0',
                            ]);
                        }


                        if ($reqType->spexgc_type == '2') {
                            $data = SpecialExternalGcrequestEmpAssign::where('spexgcemp_trid', $id);
                            foreach ($data->get() as $item) {
                                $item->update([
                                    'spexgcemp_barcode' => $specGet++,
                                ]);
                            }
                        } elseif ($reqType->spexgc_type == '1') {
                            $denoms = SpecialExternalGcrequestItem::select('specit_denoms', 'specit_qty')
                                ->where('specit_trid', $id)
                                ->orderBy('specit_id');

                            $data = SpecialExternalGcrequestEmpAssign::where('spexgcemp_trid', $id);

                            foreach ($denoms as $item) {
                                SpecialExternalGcrequestEmpAssign::create([
                                    'spexgcemp_trid' => $id,
                                    'spexgcemp_denom' => $item->specit_denoms,
                                    'spexgcemp_barcode' => $specGet++
                                ]);
                            }
                        } else {
                            return back()->with([
                                'type' => 'error',
                                'msg' => 'Opps!',
                                'description' => 'Request Type Not Found.'
                            ]);
                        }
                    });
                    return back()->with([
                        'type' => 'success',
                        'msg' => 'Nice!',
                        'description' => 'Request approved successfuly.'
                    ]);
                }
            } else {
                SpecialExternalGcrequest::where('spexgc_id', $id)
                    ->where('spexgc_status', 'pending')
                    ->update([
                        'spexgc_status' => 'cancelled',
                        'cancelRemarks' => $request->formData['cancelledRemarks'],
                        'cancelledBy' => $request->formData['cancelledBy'],
                        'cancelledDate' => now()
                    ]);
                return back()->with([
                    'type' => 'success',
                    'msg' => 'Cancelled!',
                    'description' => 'The request was successfully canceled.'
                ]);
            }
        }
    }

    public function approvedGc(Request $request)
    {
        $search = $request->search;
        $data = SpecialExternalGcrequest::where('special_external_gcrequest.spexgc_status', 'approved')
            ->where('approved_request.reqap_approvedtype', 'Special External GC Approved')
            ->join('special_external_customer', 'special_external_customer.spcus_id', '=', 'special_external_gcrequest.spexgc_company')
            ->leftJoin('approved_request', 'approved_request.reqap_trid', 'special_external_gcrequest.spexgc_id')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('spexgc_num', 'like', '%' . $search . '%')
                        ->orWhere('spexgc_dateneed', 'like', '%' . $search . '%')
                        ->orWhere('reqap_date', 'like', '%' . $search . '%')
                        ->orWhere('reqap_approvedby', 'like', '%' . $search . '%')
                        ->orWhere('special_external_customer.spcus_acctname', 'like', "%{$search}%")
                        ->orWhere('special_external_gcrequest.spexgc_datereq', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('spexgc_id')
            ->paginate(10)
            ->withQueryString();

        $data->transform(function ($item) {
            $item->dateValid = Date::parse($item->spexgc_dateneed)->format('F d Y');
            $item->dateReq = Date::parse($item->spexgc_datereq)->format('F d Y');
            return $item;
        });

        $columns = array_map(
            fn($name, $field) => ColumnHelper::arrayHelper($name, $field),
            ['RFSEGC #', 'DATE REQUESTED', 'DATE VALIDITY', 'CUSTOMER', 'DATE APPROVED', 'APPROVED BY'],
            ['spexgc_num', 'dateReq', 'dateValid', 'spcus_acctname', 'reqap_date', 'reqap_approvedby', 'View']
        );



        return Inertia::render('Finance/ApprovedGcRequest', [
            'data' => $data,
            'columns' => $columns,
        ]);
    }

    public function budgetPendingIndex()
    {
        return inertia('Finance/PendingBudgetRequest', [
            'record' => $this->financeService->pendingBudgetGc(),
            'columns' => ColumnHelper::$pending_budget_request_columns,
        ]);
    }

    public function setupBudget(Request $request)
    {

        $data = $this->financeService->budgetRequest($request);

        return inertia('Finance/SetupBudgetRequest', [
            'record' => $data->record,
            'preapp' => $data->preapp,
            'assigny' => $data->assigny,
        ]);
    }

    public function submitBudget(Request $request)
    {
        return $this->financeService->submitBudget($request);
    }

    public function approvedBudget(Request $request)
    {
        return inertia('Finance/ApprovedBudget', [
            'record' => $this->financeService->getApprovedBudget($request),
            'columns' => ColumnHelper::$approved_budget_request,
        ]);
    }
    public function approvedBudgetDetails($id)
    {
        return $this->financeService->getApprovedBudgetDetails($id);
    }

    public function selectedapprovedGc(Request $request)
    {
        $query = SpecialExternalGcrequest::select([
            'special_external_gcrequest.spexgc_id',
            'special_external_gcrequest.spexgc_num',
            DB::raw("CONCAT(req.firstname, ' ', req.lastname) as reqby"),
            'special_external_gcrequest.spexgc_datereq',
            'special_external_gcrequest.spexgc_dateneed',
            'special_external_gcrequest.spexgc_remarks',
            'special_external_gcrequest.spexgc_payment',
            'special_external_gcrequest.spexgc_paymentype',
            'special_external_gcrequest.spexgc_payment_arnum',
            'special_external_customer.spcus_companyname',
            'approved_request.reqap_remarks',
            'approved_request.reqap_doc',
            'approved_request.reqap_checkedby',
            'approved_request.reqap_approvedby',
            'approved_request.reqap_preparedby',
            'approved_request.reqap_date',
            DB::raw("CONCAT(prep.firstname, ' ', prep.lastname) as prepby"),
        ])
            ->join('users as req', 'req.user_id', '=', 'special_external_gcrequest.spexgc_reqby')
            ->join('special_external_customer', 'special_external_customer.spcus_id', '=', 'special_external_gcrequest.spexgc_company')
            ->join('approved_request', 'approved_request.reqap_trid', '=', 'special_external_gcrequest.spexgc_id')
            ->join('users as prep', 'prep.user_id', '=', 'approved_request.reqap_preparedby')
            ->where('special_external_gcrequest.spexgc_status', 'approved')
            ->where('special_external_gcrequest.spexgc_id', $request->id)
            ->where('approved_request.reqap_approvedtype', 'Special External GC Approved')
            ->get();
        $query->transform(function ($item) {
            $item->dateRequested = Date::parse($item['spexgc_datereq'])->format('F d Y');
            $item->dateNeeded = Date::parse($item['spexgc_dateneed'])->format('F d Y');
            $item->dateApproved = Date::parse($item->reqap_date)->format('F d Y');
            $item->preparedBy = ucwords($item->prepby);
            return $item;
        });

        $barcode = SpecialExternalGcrequestEmpAssign::where('spexgcemp_trid', $request->id)->get();

        $barcode->transform(function ($item) {
            $item->fullname = ucwords($item->spexgcemp_fname . ' ' . $item->spexgcemp_mname . ' ' . $item->spexgcemp_lname);
            return $item;
        });


        return response()->json([
            'data' => $query,
            'barcodes' => $barcode
        ]);
    }
    public function reprint($id)
    {
        $files = Storage::files('public/generatedTreasuryPdf/FinanceBudgetRequest');

        $filePath = null;

        foreach ($files as $file) {
            if (strpos($file, $id) !== false) {
                $filePath = $file;
                break;
            }
        }

        if ($filePath) {
            return Storage::download($filePath);
        }

        return response()->json([
            'status' => 'error',
            'msg' => 'File not found',
            'title' => 'Not Found!'
        ], 404);
    }

    public function list(Request $request)
    {
        return inertia('Marketing/specialgc/Cancelledspexgc', [
            'data' => SpecialExternalGcrequest::where('spexgc_status', 'cancelled')
                ->join('special_external_customer', 'special_external_customer.spcus_id', 'special_external_gcrequest.spexgc_company')
                ->whereAny([
                    'spexgc_id',
                ], 'like', $request->data . '%')
                ->paginate()
                ->withQueryString()
        ]);
    }

    public function view(Request $request)
    {


        $data = SpecialExternalGcrequest::where('spexgc_status', 'cancelled')
            ->where('special_external_gcrequest.spexgc_id', $request->id)
            ->join('special_external_customer', 'special_external_customer.spcus_id', 'special_external_gcrequest.spexgc_company')
            ->first();
        $requestedBy = User::where('user_id', $data['spexgc_reqby'])->first();

        $data['requested_by'] = $requestedBy['full_name'];

        return response()->json($data);
    }

    public function budgetAdjustmentsPending()
    {
        return inertia('Finance/BudgetAdjustmentsPendingComponent', [
            'records' => $this->financeService->getBudgetAdjustmentsData(),
        ]);
    }

    public function budgetAdjustmentsApproval($id)
    {
        return inertia('Finance/BudgetAdjustmentApproval', [
            'request' => $this->financeService->getBudgetApprovalData($id),
            'promo' => $this->financeService->getPromoApprovedData($id),
            'assigned' => $this->financeService->getAssigners(),
            'id' => $id,
        ]);
    }


    public function budgetAdjustmentSubmission(Request $request)
    {
        return $this->financeService->bugdetAdSubmission(collect($request));
    }
}
