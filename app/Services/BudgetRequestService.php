<?php

namespace App\Services;
use App\Models\BudgetRequest;
use Illuminate\Database\Eloquent\Collection;

class BudgetRequestService
{

    public static function pendingRequest(): Collection //pending_budget_request
    {

        $dept = request()->user()->usertype;
        $type = $dept == 2 ? 1 : $dept == 6 ? 2 : $dept;
        $record = BudgetRequest::with('user.accessPage')->where([['br_request_status', '0'], ['br_type', $type]])
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
        $record = BudgetRequest::with('user')
                    ->leftJoin('approved_budget_request', 'budget_request.br_id', '=', 'approved_budget_request.abr_budget_request_id')
                    ->where('br_request_status', '1')->get();
        return $record;
    }



    public static function cancelledRequest(): Collection //cancelled-budget-request.php
    {

        $record = BudgetRequest::with('user')
            ->join('cancelled_budget_request', 'budget_request.br_id', '=', 'cancelled_budget_request.cdreq_req_id')
            ->join('user as cancelled_user', 'budget_request.cdreq_by', '=', 'cancelled_budget_request.user_id')
        ->where('br_request_status', '2')->get();

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