<?php

namespace App\Services\Treasury\Dashboard;

use App\Helpers\NumberHelper;
use App\Models\LedgerBudget;
use App\Services\Documents\FileHandler;
use Illuminate\Support\Facades\Date;
use App\Http\Resources\BudgetRequestResource;
use App\Models\BudgetRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BudgetRequestService extends FileHandler
{

    public function __construct()
    {
        parent::__construct();
        $this->folderName = "budgetRequestScanCopy";
    }
    public function pendingRequest() //pending_budget_request
    {
        $type = userDepartment(request()->user());

        return BudgetRequest::with(['user:user_id,firstname,lastname,usertype', 'user.accessPage:access_no,title'])
            ->select('br_request', 'br_no', 'br_requested_by', 'br_remarks', 'br_file_docno', 'br_id', 'br_requested_at', 'br_requested_needed', 'br_group', 'br_preapprovedby')
            ->where([['br_request_status', '0'], ['br_type', $type]])
            ->orderBy('br_id')
            ->first();
    }
    public function cancelledRequest(Request $request)
    {
        return BudgetRequest::with([
            'user:user_id,firstname,lastname',
            'cancelledBudgetRequest' => function ($q) {
                $q->with('user:user_id,firstname,lastname')->select('cdreq_id', 'cdreq_req_id', 'cdreq_at', 'cdreq_by');
            },
        ])
            ->select('br_id', 'br_no', 'br_requested_at', 'br_request', 'br_requested_by')
            ->where('br_request_status', '2')
            ->paginate()
            ->withQueryString();
    }
    public function approvedRequest(Request $request)
    {
        return BudgetRequest::with(['user:user_id,firstname,lastname', 'approvedBudgetRequest:abr_id,abr_budget_request_id,abr_approved_by,abr_approved_at'])
            ->select('br_id', 'br_request', 'br_requested_at', 'br_no', 'br_requested_by')
            ->filter($request->only('search', 'date'))
            ->where('br_request_status', '1')
            ->orderByDesc('br_requested_at')
            ->paginate(10)
            ->withQueryString();
    }
    public function viewApprovedRequest(BudgetRequest $id)
    {
        $record = $id->load(['user:user_id,firstname,lastname', 'approvedBudgetRequest.user:user_id,firstname,lastname']);
        return new BudgetRequestResource($record);
    }
    public function submitBudgetEntry(BudgetRequest $id, Request $request)
    {
        // $request->validate([
        // 	'file' => 'required|image|mimes:jpeg,png,jpg|max:5048'
        // ]);
        if ($id->br_request_status != 0) {
            return redirect()->back()->with('error', 'Budget request already approved/cancelled.');
        }
        $filename = $this->replaceFile($request);

        $res = $id->update([
            'br_requested_by' => $request->updatedById,
            'br_request' => $request->budget,
            'br_remarks' => $request->remarks,
            'br_requested_needed' => $request->dateNeeded,
            'br_file_docno' => $filename,
            'br_group' => $request->group ?? 0,
        ]);

        if ($res) {
            return redirect()->back()->with('success', 'Budget request Successfully submitted.');
        } else {
            return redirect()->back()->with('error', 'Something went wrong while updating..');
        }
    }
    public function downloadDocument($file)
    {
        return $this->download($file);
    }
    public function viewCancelledRequest(BudgetRequest $id)
    {

        return $id->load(['cancelled_budget_request', 'user', 'cancelled_budget_request.user']);
        //Untested
        //Gamiti nig Api resource
    }

    public function budgetRequestSubmission(Request $request)
    {
        if ($this->validateField($request)) {
            return redirect()->back()->with('error', 'You have pending budget request');
        }
        $dept = userDepartment($request->user());

        $filename = $this->createFileName($request);

        $insertData = BudgetRequest::create([
            'br_request' => $request->budget,
            'br_no' => $request->br,
            'br_requested_by' => $request->user()->user_id,
            'br_requested_at' => now(),
            'br_requested_needed' => $request->dateNeeded,
            'br_file_docno' => $filename,
            'br_remarks' => $request->remarks,
            'br_request_status' => '0',
            'br_type' => $dept,
            'br_group' => 0,
            'br_category' => $request->category
        ]);

        if ($insertData->wasRecentlyCreated) {
            $this->saveFile($request, $filename);

            $stream = $this->generatePdf($request);
            return redirect()->back()->with(['stream' => $stream, 'success' => 'Successfully Submitted!']);
        }
        return redirect()->back()->with('error', 'Something went wrong with generating PDF, please try again later');
    }

    private function validateField(Request $request)
    {
        $request->validate([
            "br" => 'required',
            "category" => 'required',
            // "dateNeeded" => 'required|date',
            "budget" => 'required|not_in:0',
            "remarks" => 'required',
            // 'file' => 'required|image|mimes:jpeg,png,jpg|max:5048'
        ]);
        return BudgetRequest::where('br_checked_by', null)
            ->where('br_requested_by', '!=', '')
            ->where('br_request_status', '0')->exists();
    }

    private function generatePdf(Request $request)
    {
        // dd()
        $data = [
            'pr' => $request->br,
            'budget' => NumberHelper::format($this->budgetLedgerRegOrSpGc($request)),
            'dateRequested' => today()->toFormattedDateString(),
            // 'dateNeeded' => Date::parse($request->dateNeeded)->toFormattedDateString(),
            'remarks' => $request->remarks,

            'subtitle' => 'Revolving Budget Entry Form',

            'budgetRequested' => $request->budget,

            //signatures
            'signatures' => [
                'preparedBy' => [
                    'name' => $request->user()->full_name,
                    'position' => 'Sr Cash Clerk'
                ]
            ]
        ];
        $pdf = Pdf::loadView('pdf.giftcheck', ['data' => $data]);

        //store pdf in storage
        $this->folderName = 'generatedTreasuryPdf/BudgetRequest';


        $this->savePdfFile($request, $request->br, $pdf->output());

        return base64_encode($pdf->output());
    }

    private function budgetLedgerRegOrSpGc($request)
    {
        $query = LedgerBudget::select(DB::raw('SUM(bdebit_amt) as debit'), DB::raw('SUM(bcredit_amt) as credit'))
            ->whereNot('bcus_guide', 'dti')
            ->where('bledger_category', $request->category)
            ->first();

        return bcsub($query->debit, $query->credit, 2);
    }
}
