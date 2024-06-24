<?php

namespace App\Services\Treasury\Dashboard;

use App\Http\Resources\BudgetLedgerApprovedResource;
use App\Models\BudgetRequest;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class BudgetRequestService
{

	public static function pendingRequest(): Collection //pending_budget_request
	{

		$dept = request()->user()->usertype;
		$type = $dept == 2 ? 1 : $dept == 6 ? 2 : $dept;
		// $type = 2;
		$record = BudgetRequest::with(['user:user_id,firstname,lastname,usertype', 'user.accessPage:access_no,title'])
			->select('br_request', 'br_no', 'br_requested_by', 'br_remarks', 'br_file_docno', 'br_id', 'br_requested_at', 'br_requested_needed', 'br_group', 'br_preapprovedby')
			->where([['br_request_status', '0'], ['br_type', $type]])
			->orderBy('br_id')
			->first();

		return $record;
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
}