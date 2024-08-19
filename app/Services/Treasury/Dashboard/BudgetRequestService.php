<?php

namespace App\Services\Treasury\Dashboard;

use App\Models\LedgerBudget;
use App\Services\Documents\UploadFileHandler;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\BudgetRequestResource;
use App\Models\BudgetRequest;
use App\Services\Treasury\ColumnHelper;
use Illuminate\Http\Request;

class BudgetRequestService extends UploadFileHandler
{

	public function __construct()
	{
		parent::__construct();
		$this->folderName = "BudgetRequestScanCopy";
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
			'requestedUser:user_id,firstname,lastname',
			'cancelledBudgetRequest.user:user_id,firstname,lastname',
			'cancelledBudgetRequest:cdreq_id,cdreq_req_id,cdreq_at, cdreq_by'
		])
			->where('br_request_status', '2')
			->paginate()
			->withQueryString();
	}
	public function approvedRequest(Request $request)
	{
		return BudgetRequest::with(['user', 'approvedBudgetRequest'])
			->filter($request->only('search', 'date'))
			->where('br_request_status', '1')
			->orderByDesc('br_requested_at')
			->paginate()
			->withQueryString();
	}
	public function viewApprovedRequest(BudgetRequest $id)
	{
		$record = $id->load(['user:user_id,firstname,lastname', 'approvedBudgetRequest.user:user_id,firstname,lastname']);
		return new BudgetRequestResource($record);
	}
	public function submitBudgetEntry(BudgetRequest $id, Request $request)
	{
		$request->validate([
			'file' => 'required|image|mimes:jpeg,png,jpg|max:5048'
		]);

		if ($id->br_request_status != 0) {
			session()->flash('error', 'Budget request already approved/cancelled.');
			return redirect()->back();
		}
		$filename = $this->handleUpload($request);
		$this->updateTable($request, $id, $filename);

		return redirect()->back();

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
		$ableToRequest = BudgetRequest::whereRelation('user', 'usertype', $request->user()->usertype)
			->where('br_request_status', 0)
			->count();

		if ($ableToRequest) {
			return redirect()->back()->with('error', 'You have pending budget request');
		}

		$request->validate([
			"br" => 'required',
			"dateNeeded" => 'required|date',
			"budget" => 'required|not_in:0',
			"remarks" => 'required',
			'file' => 'required|image|mimes:jpeg,png,jpg|max:5048'
		]);

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
			'br_group' => 0
		]);

		if ($insertData->wasRecentlyCreated) {
			$this->saveFile($request, $filename);
			return redirect()->back()->with('success', 'SuccessFully Submitted');
		}
		return redirect()->back()->with('error', 'Something went wrong, please try again later');
	}
}
