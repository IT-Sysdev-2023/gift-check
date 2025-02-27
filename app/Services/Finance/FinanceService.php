<?php

namespace App\Services\Finance;

use App\Helpers\NumberHelper;
use App\Models\ApprovedAdjustmentRequest;
use App\Models\ApprovedBudgetRequest;
use App\Models\Assignatory;
use App\Models\BudgetAdjustment;
use App\Models\BudgetRequest;
use App\Models\CancelledAdjRequest;
use App\Models\CancelledBudgetRequest;
use App\Models\LedgerBudget;
use App\Models\PromogcPreapproved;
use App\Models\User;
use App\Services\Documents\FileHandler;
use Barryvdh\DomPDF\Facade\Pdf;
use Faker\Core\Number;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;

class FinanceService extends FileHandler
{
    public function __construct()
    {
        parent::__construct();

        $this->folderName = "financeUpload";
    }

    public function uploadFileHandler($request)
    {
        // dd($request->file);
        $name = $this->getOriginalFileName($request, $request->file);

        return $this->saveFile($request, $name);
    }



    public function pendingBudgetGc()
    {

        $data = BudgetRequest::select('br_no', 'br_id', 'br_requested_at', 'br_request', 'br_requested_needed', 'br_requested_by')
            ->with('user:user_id,firstname,lastname')->where('br_request_status', '0')
            ->where('br_checked_by', '!=', null)
            ->where('br_requested_by', '!=', '')
            ->paginate(10)->withQueryString();

        $data->transform(function ($item) {
            $item->fullname = $item->user->full_name;
            $item->needed = Date::parse($item->br_requested_needed)->toFormattedDateString();
            $item->req_at = Date::parse($item->br_requested_at)->toFormattedDateString();
            return $item;
        });

        return $data;
    }

    public function budgetRequest($request)
    {
        // dd();
        $budget = BudgetRequest::with('user:user_id,firstname,lastname,usertype', 'user.accessPage:access_no,title')
            ->where('br_id', $request->id)
            ->first();

        $users = new User();

        $reqby = $users->select('firstname', 'lastname')->where('user_id', $budget->br_requested_by)->value('full_name');
        $checkby = $users->select('firstname', 'lastname')->where('user_id', $budget->br_checked_by)->value('full_name');

        if ($budget) {
            $budget->time = Date::parse($budget->br_requested_at)->format('h:i');
            $budget->reqdate = Date::parse($budget->br_requested_at)->toFormattedDateString();
            $budget->needed = Date::parse($budget->br_requested_needed)->toFormattedDateString();
            $budget->reqby = $reqby;
            $budget->checkby = $checkby;
        }


        $preApproved = PromogcPreapproved::with('user:user_id,firstname,lastname')->where('prapp_reqid', $request->id)->get();

        $assignatories = Assignatory::select('assig_position', 'assig_name', 'assig_id')->where('assig_dept', $request->user()->usertype)->orWhere('assig_dept', '1')->get();

        return (object) [
            'record' => $budget,
            'preapp' => $preApproved,
            'assigny' => $assignatories,
        ];
    }

    public function submitBudget($request)
    {
        $request->validate([
            'br_select' => 'required',
            'br_checkby' => 'required',
            'br_remarks' => 'required',
            'br_appby' => 'required',
        ]);


        if ($request->br_select == '1') {
            if ($request->br_group == '1') {
                if ($request->br_preappby != 1) {
                    return back()->with([
                        'title' => 'Error',
                        'msg' => 'Budget Request Needs Recommendation Approval from Retail Group' . $request->br_group,
                        'status' => 'error',
                    ]);
                }
            } else {

                $this->legderBudget($request);
            }
        } else {
            $this->cancelBudgetRequest($request);
        }
    }

    private function legderBudget($request)
    {
        $ledger = LedgerBudget::max('bledger_no') + 1;

        DB::transaction(function () use ($request, $ledger) {

            $file = $this->createFileName($request);
            // dd($request->all());
            LedgerBudget::create([
                'bledger_no' => NumberHelper::leadingZero($ledger, "%013d"),
                'bledger_trid' => $request->br_id,
                'bledger_datetime' => now(),
                'bledger_type' => 'RFBR',
                'bdebit_amt' => $request->br_req,
                'bledger_typeid' => $request->br_budtype,
                'bledger_group' => $request->br_group,
                'bledger_category' => $request->br_category
            ]);

            ApprovedBudgetRequest::create([
                'abr_budget_request_id' => $request->br_id,
                'abr_approved_by' => $request->br_appby,
                'abr_checked_by' => $request->br_checkby,
                'approved_budget_remark' => $request->br_remarks,
                'abr_approved_at' => now(),
                'abr_file_doc_no' => $file ?? '',
                'abr_prepared_by' => $request->user()->user_id,
                'abr_ledgerefnum' => NumberHelper::leadingZero($ledger, "%013d"),
            ]);

            if (BudgetRequest::where('br_id', $request->br_id)->value('br_request_status') == 0) {

                $isTrue = BudgetRequest::where('br_id', $request->br_id)->where('br_request_status', '0')->update([
                    'br_request_status' => $request->br_select
                ]);

                if ($isTrue) {

                    $this->saveFile($request, $file);

                    $stream = $this->generatePdf($request);

                    return redirect()->back()->with([
                        'stream' => $stream,
                        'msg' => 'SuccessFully Submitted!',
                        'status' => 'success',
                        'title' => 'Success'
                    ]);
                } else {
                    return back()->with([
                        'title' => 'Warning',
                        'msg' => 'Budget Request already approved/cancelled',
                        'status' => 'warning',
                    ]);
                }
            } else {

                return back()->with([
                    'title' => 'Warning',
                    'msg' => 'Budget Request already approved/cancelled',
                    'status' => 'warning',
                ]);
            }
        });
    }

    private function generatePdf($request)
    {
        $bud = BudgetRequest::where('br_id', $request->br_id)->first();

        $appby = User::select('firstname', 'lastname')->where('user_id', $bud->br_requested_by)->value('full_name');
        $checkby = User::select('firstname', 'lastname')->where('user_id', $bud->br_checked_by)->value('full_name');

        $data = [
            'pr' => $bud->br_no,
            'budget' => NumberHelper::format(LedgerBudget::budget()),
            'dateRequested' => today()->toFormattedDateString(),
            'dateNeeded' => Date::parse($bud->br_requested_needed)->toFormattedDateString(),
            'remarks' => $bud->br_remarks,

            'subtitle' => 'Revolving Budget Entry Form',

            'budgetRequested' => $bud->br_request,
            //signatures

            'signatures' => [
                'preparedBy' => [
                    'name' => $appby,
                    'position' => 'Sr Cash Clerk'
                ],
                'checkedBy' => [
                    'name' => $checkby,
                    'position' => 'Department Head'
                ],
                'reviewedBy' => [
                    'name' => $request->user()->full_name,
                    'position' => 'Financial Analyst'
                ],
            ]
        ];
        $pdf = Pdf::loadView('pdf.giftcheck', ['data' => $data]);

        $this->folderName = 'generatedTreasuryPdf/FinanceBudgetRequest';

        $this->savePdfFile($request, $bud->br_no, $pdf->output());

        return base64_encode($pdf->output());
    }

    private function cancelBudgetRequest($request)
    {
        DB::transaction(function () use ($request) {

            $isTrue =  BudgetRequest::where('br_id', $request->br_id)->where('br_request_status', '0')->update([
                'br_request_status' => $request->br_select
            ]);

            if ($isTrue) {
                CancelledBudgetRequest::create([
                    'cdreq_req_id' => $request->br_id,
                    'cdreq_at' => now(),
                    'cdreq_by' => $request->user()->user_id,
                ]);

                return redirect()->route('finance.budget.pending')->with([
                    'title' => 'Cancelled',
                    'msg' => 'Budget Request Cancelled',
                    'status' => 'success',
                ]);
            } else {
                return back()->with([
                    'title' => 'Warning',
                    'msg' => 'Budget Request already approved/cancelled',
                    'status' => 'warning',
                ]);
            }
        });
    }

    public function getApprovedBudget($request)
    {
        $search = $request->search;
        $data = BudgetRequest::select(
            'br_id',
            'br_request',
            'br_requested_at',
            'br_no',
            'abr_approved_by',
            'abr_approved_at',
            'br_requested_by',
            'br_request_status',
        )
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('br_id', 'like', '%' . $search . '%')
                        ->orWhere('br_request', 'like', '%' . $search . '%')
                        ->orWhere('br_requested_at', 'like', '%' . $search . '%')
                        ->orWhere('br_no', 'like', '%' . $search . '%')
                        ->orWhere('abr_approved_by', 'like', '%' . $search . '%')
                        ->orWhere('abr_approved_at', 'like', '%' . $search . '%')
                        ->orWhere('br_requested_by', 'like', '%' . $search . '%')
                        ->orWhere('br_request_status', 'like', '%' . $search . '%')
                        ->orWhereHas('user', function ($query) use ($search) {
                            $query->whereRaw("CONCAT(firstname, ' ', lastname) LIKE ?", ['%' . $search . '%']);
                        });
                });
            })
            ->with('user:user_id,firstname,lastname')
            ->leftJoin('approved_budget_request', 'abr_budget_request_id', '=', 'br_id')
            ->where('br_request_status', '1')
            ->orderByDesc('br_no')
            ->paginate(10)
            ->withQueryString();

        $data->transform(function ($item) {
            $item->fullname = $item->user->full_name;
            $item->requestDate = Date::parse($item->br_requested_at)->toFormattedDateString();
            $item->approvedAt = Date::parse($item->abr_approved_at)->toFormattedDateString();
            $item->budgetReq = NumberHelper::currency($item->br_request);
            $item->checkedby =  User::select('firstname', 'lastname')->where('user_id', $item->abr_approved_by)->value('full_name') ?? '--Mao ning Daan--';
            return $item;
        });

        return $data;
    }

    public function getApprovedBudgetDetails($id)
    {

        $data = BudgetRequest::with('user:user_id,firstname,lastname')
            ->selectFilter()
            ->where('br_request_status', '1')
            ->leftJoin('approved_budget_request', 'abr_budget_request_id', '=', 'br_id')
            ->leftJoin('users', 'user_id', '=', 'abr_prepared_by')
            ->where('br_id', $id)->first();

        if ($data) {
            $data->apreqat = Date::parse($data->abr_approved_at)->toFormattedDateString();
            $data->needed = Date::parse($data->br_requested_needed)->toFormattedDateString();
            $data->reqat = Date::parse($data->br_requested_at)->toFormattedDateString();
            $data->time = Date::parse($data->br_requested_at)->format('h:i');
            $data->budgetReq = NumberHelper::currency($data->br_request);
            $data->checkedby =  User::select('firstname', 'lastname')->where('user_id', $data->abr_approved_by)->value('full_name');
            $data->appby =  User::select('firstname', 'lastname')->where('user_id', $data->abr_checked_by)->value('full_name');
        }
        return $data;
    }

    public function getBudgetAdjustmentsData()
    {
        $data = BudgetAdjustment::select(
            'adj_id',
            'adj_request',
            'adj_requested_at',
            'adj_no',
            'adjust_type',
            'adj_requested_by'
        )
            ->with('user:user_id,firstname,lastname')
            ->where('adj_request_status', '0')
            ->get();

        $data->transform(function ($item) {
            return (object) [
                'id' => $item->adj_id,
                'request' => $item->adj_request,
                'requestAt' => $item->adj_requested_at,
                'reqno' => $item->adj_no,
                'type' => $item->adjust_type,
                'reqby' => $item->user->full_name,
            ];
        });
        // dd()

        return $data;
    }

    public function getBudgetApprovalData($id)
    {
        return BudgetAdjustment::select(
            'adj_request',
            'adj_no',
            'adj_requested_by',
            'adj_remarks',
            'adj_file_docno',
            'adj_id',
            'adj_requested_at',
            'adjust_type',
            'adj_type',
            'adj_group',
            'adj_preapprovedby',
        )->with('user:user_id,firstname,lastname,usertype', 'user.accessPage:access_no,title')
            ->where('adj_request_status', '0')
            ->where('adj_id', $id)
            ->first();
    }

    public function getPromoApprovedData($id)
    {
        return PromogcPreapproved::select('prapp_by', 'prapp_doc', 'prapp_remarks', 'prapp_at')
            ->with('user:user_id,firstname,lastname')
            ->where('prapp_reqid', $id)
            ->first();
    }
    public function getAssigners()
    {

        $data = Assignatory::whereIn('assig_dept', [request()->user()->usertype, '1'])->get();

        $data->transform(function ($item) {
            return (object) [
                'value' => $item->assig_id,
                'label' => $item->assig_name,
            ];
        });

        return $data;
    }

    private function currentBudget()
    {
        $ledger = LedgerBudget::select('bdebit_amt', 'bcredit_amt')->where('bcus_guide', '!=', 'dti')->get();

        $debit = $ledger->sum('bdebit_amt');
        $credit = $ledger->sum('bcredit_amt');

        return $debit - $credit;
    }

    public function getLedgerNumber()
    {
        return LedgerBudget::orderBy('bledger_id')->take(1)->value('bledger_no') ?? 1;
    }

    public function bugdetAdSubmission($request)
    {

        // dd($request);
        if ($request['atype'] === 'negative') {

            $entry = 'bcredit_amt';

            if ($request['adjrequest'] > $this->currentBudget()) {
                return response()->json([
                    'status' => 'error',
                    'title' => 'Amount is greater than current budget'
                ]);
            }
        } else {
            $entry = 'bdebit_amt';
        }

        if ($request['status'] === '1') {
            if ($request['bgroup'] === '1') {
                if ($request['recapp'] !== '1') {
                    $approved = false;
                }
            }

            if ($approved) {

                $this->folderName = 'approvedAdjustmentRequest';


                DB::transaction(function () use ($request, $entry) {

                    $file =  $this->createFileName($request);

                    LedgerBudget::create([
                        'bledger_no' => $this->getLedgerNumber(),
                        'bledger_trid' => $request['id'],
                        'bledger_datetime' => now(),
                        'bledger_type' => 'BA',
                        $entry => $request['adjrequest'],
                        'bledger_typeid' =>  $request['btype'],
                        'bledger_group' => $request['bgroup'],
                    ]);

                    ApprovedAdjustmentRequest::create([
                        'app_adj_request_id' => $request['id'],
                        'app_approved_by' => $request['appby'],
                        'app_checked_by' => $request['checkby'],
                        'app_approved_at' => now(),
                        'app_prepared_by' => request()->user()->user_id,
                        'app_adj_remark' => $request['remarks'],
                        'app_file_doc_no' => $file ?? '',
                        'app_ledgerefnum' => $this->getLedgerNumber(),
                    ]);

                    if ($this->getAdjustmentBudget($request['id']) === 0) {
                        BudgetAdjustment::where('adj_id', $request['id'])->where('adj_request_status', '0')
                            ->update([
                                'adj_request_status' => $request['status']
                            ]);
                    } else {
                        return response()->json([
                            'status' => 'error',
                            'title' => 'Error',
                            'msg' => 'Adjustment Request already approved/cancelled'
                        ]);
                    }

                    $this->saveFile($request, $file);
                });
            } else {
                return response()->json([
                    'status' => 'error',
                    'title' => 'Error',
                    'msg' => 'Budget Adjustment Needs Recommendation Approval from Retail Group ' . $request['bgroup'] . '.'
                ]);
            }
        }else{
            DB::transaction(function () use ($request) {
                BudgetAdjustment::where('adj_id', $request['id'])->where('adj_request_status', '0')->update([
                    'adj_request_status' => $request['status'],
                ]);

              $cancelled =  CancelledAdjRequest::create([
                    'cadj_req_id' => $request['id'],
                    'cadj_at' => now(),
                    'cadj_by' => request()->user()->user_id,
                ]);

                if($cancelled->wasRecentlyCreated){
                    return response()->json([
                        'status' => 'error',
                        'title' => 'Error',
                        'msg' => 'Budget Adjustment request cancelled.'
                    ]);
                }else{
                    return response()->json([
                        'status' => 'error',
                        'title' => 'Error',
                        'msg' => 'Budget Adjustment already approved/cancelled'
                    ]);
                }
            });
        }
    }


    private function getAdjustmentBudget($id)
    {
        return BudgetAdjustment::where('adj_id', $id)->value('adj_request_status');
    }
    // public function bugdetAdSubmission($request)
    // {
    //     dd($request->all());
    // }
}
