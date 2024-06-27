<?php

namespace App\Services\Treasury\Dashboard;

use App\Models\LedgerBudget;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\BudgetRequestResource;
use App\Models\BudgetRequest;
use App\Services\Treasury\ColumnHelper;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class BudgetRequestService
{

	public function pendingRequest() //pending_budget_request
	{
		$dept = request()->user()->usertype;

		$type = match($dept) {
			'2' => 1,
			'6' => 2,
			default => $dept
		};
		
		$record = BudgetRequest::with(['user:user_id,firstname,lastname,usertype', 'user.accessPage:access_no,title'])
			->select('br_request', 'br_no', 'br_requested_by', 'br_remarks', 'br_file_docno', 'br_id', 'br_requested_at', 'br_requested_needed', 'br_group', 'br_preapprovedby')
			->where([['br_request_status', '0'], ['br_type', $type]])
			->orderBy('br_id')
			->first();

			return inertia(
				'Treasury/BudgetRequest/PendingRequest',
				[
					// 'filters' => $request->all('search', 'date'),
					'currentBudget' => LedgerBudget::currentBudget(),
					'title' => 'Update Budget Entry Form',
					'data' => $record,
					// 'columns' => ColumnHelper::$approved_buget_request,
				]
	
			);
	}



	public static function cancelledRequest(): Collection //cancelled-budget-request.php
	{

		$record = BudgetRequest::with([
			'requestedUser:user_id,firstname,lastname',
			'cancelledBudgetRequest.user:user_id,firstname,lastname',
			'cancelledBudgetRequest:cdreq_id,cdreq_req_id,cdreq_at, cdreq_by'
		])
			->select('br_id', 'br_requested_by', 'br_request_status', 'br_no', 'br_requested_at', 'br_request')
			->where('br_request_status', '2')
			->get();

		return $record;
	}

	public function budgetRequestApproved(Request $request)
	{
		$record = BudgetRequest::with(['user', 'approvedBudgetRequest'])
			// ->leftJoin('approved_budget_request', 'budget_request.br_id', 'approved_budget_request.abr_budget_request_id')
			// ->select('users.lastname', 'users.firstname', 'br_request', 'br_no', 'br_id', 'br_requested_at', 'abr_approved_by', 'abr_approved_at')
			->filter($request->only('search', 'date'))
			->where('br_request_status', '1')
			->orderByDesc('br_requested_at')
			->paginate()
			->withQueryString();
		return inertia(
			'Treasury/BudgetRequest/TableApproved',
			[
				'filters' => $request->all('search', 'date'),
				'title' => 'Approved Budget Request',
				'data' => BudgetRequestResource::collection($record),
				'columns' => ColumnHelper::$approved_buget_request,
			]

		);
	}

	public function viewBudgetRequestApproved(BudgetRequest $budgetRequest)
	{
		$record = $budgetRequest->load(['user:user_id,firstname,lastname', 'approvedBudgetRequest.user:user_id,firstname,lastname']);
		$data = new BudgetRequestResource($record);
		return response()->json($data);
	}

	public function submitBudgetEntry(Request $request){
		// dd("{$request->user()->user_id}-" . now()->toDateTimeString() . ".jpg");
		$request->validate([
			'file' => 'image|mimes:jpeg,png,jpg|max:5048'
		]);

		$disk = Storage::disk('public');
		
		if(!is_null($request->file)){
			$disk->putFileAs('BudgetRequestScanCopy', $request->file, "{$request->user()->user_id}-" . now()->format('Y-m-d-His') . ".jpg");
		}

		dd($disk);
		
	}
}