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
		$this->folderName = "BudgetRequestScanCopy/";
	}
	public function pendingRequest() //pending_budget_request
	{
		$dept = request()->user()->usertype;

		$type = match ($dept) {
			'2' => 1,
			'6' => 2,
			default => $dept
		};

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
			// ->select('br_id', 'br_requested_by', 'br_request_status', 'br_no', 'br_requested_at', 'br_request')
			->where('br_request_status', '2')
			->paginate()
			->withQueryString();	
	}
	public function approvedRequest(Request $request)
	{
		return BudgetRequest::with(['user', 'approvedBudgetRequest'])
			// ->leftJoin('approved_budget_request', 'budget_request.br_id', 'approved_budget_request.abr_budget_request_id')
			// ->select('users.lastname', 'users.firstname', 'br_request', 'br_no', 'br_id', 'br_requested_at', 'abr_approved_by', 'abr_approved_at')
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
			'file' => 'nullable|image|mimes:jpeg,png,jpg|max:5048'
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

		$record = $id->load(['cancelled_budget_request', 'user', 'cancelled_budget_request.user']);
		//Untested
		//Gamiti nig Api resource 
		return response()->json($record);

	}
}