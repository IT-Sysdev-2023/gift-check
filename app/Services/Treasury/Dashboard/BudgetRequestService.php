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

	private static $folderName = "BudgetRequestScanCopy/";
	private $disk;

	public function __construct() {
		$this->disk = Storage::disk('public');
	}
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

	public function viewBudgetRequestApproved(BudgetRequest $id)
	{
		$record = $id->load(['user:user_id,firstname,lastname', 'approvedBudgetRequest.user:user_id,firstname,lastname']);
		$data = new BudgetRequestResource($record);
		return response()->json($data);
	}

	public function submitBudgetEntry(BudgetRequest $id,Request $request){
		$request->validate([
			'file' => 'nullable|image|mimes:jpeg,png,jpg|max:5048'
		]);
		$filename = $request->document;

		if($id->br_request_status == 0){
			
			if($request->hasFile('file')){
			
				if(!is_null($request->document)){
					//delete old image
					$this->disk->delete(self::$folderName.$request->document);
				}
				//insert new image
				$filename = "{$request->user()->user_id}-" . now()->format('Y-m-d-His') . ".jpg";
				$this->disk->putFileAs(self::$folderName, $request->file, $filename);
			}
			$res = $id->update([
				'br_requested_by' => $request->updatedById,
				'br_request' => $request->budget,
				'br_remarks' => $request->remarks,
				'br_requested_needed' => $request->dateNeeded,
				'br_file_docno' => $filename,
				'br_group' => $request->group ?? 0,
			]);
	
			if($res){
				session()->flash('success', 'Update Successfully');
			}else{
				session()->flash('error', 'Something went wrong while updating..');
			}
		}else{
			session()->flash('error', 'Budget request already approved/cancelled.');
		}
		return redirect()->back();
		
	}

	public function downloadDocument($file)
	{
		if($this->disk->exists(self::$folderName.$file)){
			return $this->disk->download(self::$folderName.$file);
		}else{
			return redirect()->back()->with('error', 'File Not Found');
		}
	}
}