<?php

namespace App\Services\Finance;

use App\Helpers\NumberHelper;
use App\Models\ApprovedBudgetRequest;
use App\Models\Assignatory;
use App\Models\BudgetRequest;
use App\Models\CancelledBudgetRequest;
use App\Models\LedgerBudget;
use App\Models\PromogcPreapproved;
use App\Models\User;
use App\Services\Documents\FileHandler;
use Barryvdh\DomPDF\Facade\Pdf;
use Faker\Core\Number;
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

            LedgerBudget::create([
                'bledger_no' => NumberHelper::leadingZero($ledger, "%013d"),
                'bledger_trid' => $request->br_id,
                'bledger_datetime' => now(),
                'bledger_type' => 'RFBR',
                'bdebit_amt' => $request->br_req,
                'bledger_typeid' => $request->br_budtype,
                'bledger_group' => $request->br_group,
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
                    'position' => 'Finance Analyst'
                ],
                'reviewedBy' => [
                    'name' => $request->user()->full_name,
                    'position' => 'Finance Analyst'
                ],
            ]
        ];
        $pdf = Pdf::loadView('pdf.giftcheck', ['data' => $data]);

        $this->folderName = 'generatedTreasuryPdf/BudgetRequest';

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

    public function getApprovedBudget()
    {
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
}
