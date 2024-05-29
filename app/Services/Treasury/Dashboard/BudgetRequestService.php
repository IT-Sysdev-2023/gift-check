<?php

namespace App\Services\Treasury\Dashboard;

use App\Models\BudgetRequest;
use Illuminate\Database\Eloquent\Collection;

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

		//       WHERE
// 				`budget_request`.`br_request_status`='0'
// 			AND
// 				`budget_request`.`br_type`='$type'
// 			ORDER BY
// 				`budget_request`.`br_id`
// 			LIMIT 1

		// function getBudgetRequestForUpdateByDept($link,$dept)
// 	{
// 		if($dept==2)
// 		{
// 			$type=1;
// 		}
// 		elseif ($type=6)
// 		{
// 			$type=2;
// 		}

		// 		$query = $link->query(
// 			"SELECT 
// 				`budget_request`.`br_request`,
// 				`budget_request`.`br_no`,
// 				`budget_request`.`br_requested_by`,
// 				`users`.`firstname`,
// 				`users`.`lastname`,
// 				`budget_request`.`br_remarks`,
// 				`budget_request`.`br_file_docno`,
// 				`budget_request`.`br_id`,
// 				`budget_request`.`br_requested_at`,
// 				`budget_request`.`br_requested_needed`,
// 				`access_page`.`title`,
// 				`budget_request`.`br_group`,
// 				`budget_request`.`br_preapprovedby`
// 			FROM 
// 				`budget_request`
// 			INNER JOIN
// 				`users`
// 			ON
// 				`users`.`user_id` = `budget_request`.`br_requested_by`
// 			INNER JOIN
// 				`access_page`
// 			ON
// 				`access_page`.`access_no` = `users`.`usertype`
// 			WHERE
// 				`budget_request`.`br_request_status`='0'
// 			AND
// 				`budget_request`.`br_type`='$type'
// 			ORDER BY
// 				`budget_request`.`br_id`
// 			LIMIT 1
// 		");

		// 		if($query)
// 		{
// 			$row = $query->fetch_object();
// 			return $row;
// 		}
// 		else 
// 		{
// 			return $link->error;
// 		}		
// 	}
	}

	public static function approvedRequest(): Collection //approved-budget-request
	{
		$record = BudgetRequest::leftJoin('approved_budget_request', 'budget_request.br_id', '=', 'approved_budget_request.abr_budget_request_id')
			->select('br_request', 'br_no', 'br_id', 'abr_approved_by')
			->where('br_request_status', '1')->get();
		return $record;

		// $table = 'budget_request';
		// $select = "budget_request.br_request,
		//     budget_request.br_no,
		//     budget_request.br_id,
		//     approved_budget_request.abr_approved_by";
		// $where = 'br_request_status=1';
		// $join = 'LEFT JOIN
		//         approved_budget_request
		//     ON
		//         approved_budget_request.abr_budget_request_id = budget_request.br_id';
		// $limit = '';
		// $data = getAllData($link,$table,$select,$where,$join,$limit);
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

		//     function getAllCancelledBudgetRequest($link)
		// {
		// 	$rows = [];
		// 	$query = $link->query(
		// 	"SELECT
		// 		`budget_request`.`br_id`,
		// 		`budget_request`.`br_no`,
		// 		`budget_request`.`br_requested_at`,
		// 		`budget_request`.`br_request`,
		// 		`request_user`.`firstname` as fnamerequest,
		// 		`request_user`.`lastname` as lnamerequest,
		// 		`cancelled_budget_request`.`cdreq_at`,
		// 		`cancelled_user`.`firstname` as fnamecancelled,
		// 		`cancelled_user`.`lastname`	as lnamecancelled		
		// 	FROM 
		// 		`budget_request`
		// 	INNER JOIN
		// 		`cancelled_budget_request`
		// 	ON
		// 		`cancelled_budget_request`.`cdreq_req_id` = `budget_request`.`br_id`
		// 	INNER JOIN
		// 		`users` as `request_user`
		// 	ON
		// 		`request_user`.`user_id` = `budget_request`.`br_requested_by` 

		// 	INNER JOIN
		// 		`users` as `cancelled_user`
		// 	ON
		// 		`cancelled_user`.`user_id` = `cancelled_budget_request`.`cdreq_by`
		// 	WHERE
		// 		`budget_request`.`br_request_status`='2'
		// 	");

		// 	if($query)
		// 	{
		// 		while ($row = $query->fetch_object()) 
		// 		{
		// 			$rows[] = $row;
		// 		}
		// 		return $rows;
		// 	}
		// 	else 
		// 	{
		// 		return $link->error;
		// 	}
		// }
	}

}