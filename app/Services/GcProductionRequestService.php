<?php

namespace App\Services;

use App\Models\CancelledProductionRequest;
use App\Models\Denomination;
use App\Models\ProductionRequest;

class GcProductionRequestService
{
    public function pendingRequest() //pending_production_request.php
    {

        $dept = request()->user()->usertype;
        $pr = ProductionRequest::withWhereHas('user', fn($query) => $query->where('usertype', $dept))
            ->where('pe_status', 0)
            ->orderByDesc('pe_id')
            ->first();
        $denoms = Denomination::where([['denom_type', 'RSGC'], ['denom_status', 'active']])
            ->orderBy('denomination')->get();


        // function getPendingProductionRequestByDept($link,$dept)
        // {
        //     $query = $link->query(
        //         "SELECT
        //             `production_request`.`pe_id`,
        //             `users`.`firstname`,
        //             `users`.`lastname`,
        //             `production_request`.`pe_file_docno`,
        //             `production_request`.`pe_date_needed`,
        //             `production_request`.`pe_remarks`,
        //             `production_request`.`pe_num`,
        //             `production_request`.`pe_date_request`,
        //             `production_request`.`pe_group`

        //         FROM 
        //             `production_request`
        //         INNER JOIN
        //             `users`
        //         ON
        //             `users`.`user_id` = `production_request`.`pe_requested_by`
        //         WHERE 
        //             `production_request`.`pe_status`='0'
        //         AND
        //             `users`.`usertype`='$dept'
        //         ORDER BY 
        //             `pe_id`
        //         DESC
        //         LIMIT 1
        //     ");

        //     if($query)
        //     {
        //         $row = $query->fetch_object();
        //         return $row;
        //     }
        //     else 
        //     {
        //         return $link->error;
        //     }
        // }

        //     function getAllDenomination($link)
        // {
        // 	$rows = [];
        // 	$query = $link->query(
        // 		"SELECT
        // 			*
        // 		FROM 
        // 			denomination
        // 		WHERE 
        // 			denom_type='RSGC'
        // 		and
        // 			denom_status='active'
        // 		ORDER BY 
        // 			denomination
        // 		ASC
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
        // 		echo $link->error;
        // 		return $rows[] = $link->error;
        // 	}
        // }
    }

    protected function approvedRequest() //approved-production-request
    {

        $record = ProductionRequest::with('user')
            ->join('approved_production_request', 'production_request.pe_id', '=', 'approved_production_request.ape_pro_request_id')
            ->where('pe_status', 1)
            ->orderByDesc('pe_id')
            ->get();

        return $record;

        // function approvedProductionRequest($link)
        // {
        //     $rows = [];
        //     $query = $link->query(
        //         "SELECT 
        // 		`production_request`.`pe_id`,
        // 		`production_request`.`pe_num`,
        // 		`production_request`.`pe_date_request`,
        // 		`production_request`.`pe_date_needed`,
        // 		`approved_production_request`.`ape_approved_at`,
        // 		`approved_production_request`.`ape_approved_by`,
        // 		`userequest`.`firstname`,
        // 		`userequest`.`lastname`
        // 	FROM 
        // 		`production_request`
        // 	INNER JOIN 
        // 		`approved_production_request`
        // 	ON 
        // 		`production_request`.`pe_id` = `approved_production_request`.`ape_pro_request_id`
        // 	INNER JOIN
        // 		`users` as `userequest`
        // 	ON
        // 		`userequest`.`user_id` = `production_request`.`pe_requested_by`
        // 	WHERE 
        // 		`pe_status`='1'
        // 	ORDER BY 
        // 		`production_request`.`pe_id`
        // 	DESC
        // "
        //     );

        //     if ($query) {
        //         while ($row = $query->fetch_object()) {
        //             $rows[] = $row;
        //         }
        //         return $rows;
        //     } else {
        //         $row = $link->error;
        //     }

        // }

    }

    public function cancelledRequest() // cancelled-production-request.php
    {

        $record = CancelledProductionRequest::join('production_request', 'cancelled_production_request.cpr_pro_id', '=', 'production_request.pe_id')
            ->join('users as lreq', 'cancelled_production_request.pe_requested_by', '=', 'lreq.user_id')
            ->join('users as lcan', 'cancelled_production_request.cpr_by', '=','lcan.user_id')
            ->orderByDesc('cpr_id')
            ->get();
        return $record;
        //     function getAllCancelledProductionRequest($link)
        // {
        // 	$rows = [];
        // 	$query = $link->query(
        // 		"SELECT 
        // 			production_request.pe_id,
        // 			production_request.pe_num,
        // 			production_request.pe_date_request,
        // 			production_request.pe_date_needed,
        // 			lreq.firstname as lreqfname,
        // 			lreq.lastname as lreqlname,
        // 			cancelled_production_request.cpr_at,
        // 			lcan.firstname as lcanfname,
        // 			lcan.lastname as lcanlname
        // 		FROM 
        // 			cancelled_production_request
        // 		INNER JOIN
        // 			production_request
        // 		ON
        // 			production_request.pe_id = cancelled_production_request.cpr_pro_id
        // 		INNER JOIN
        // 			users as lreq
        // 		ON
        // 			lreq.user_id = production_request.pe_requested_by
        // 		INNER JOIN
        // 			users as lcan
        // 		ON
        // 			lcan.user_id = cancelled_production_request.cpr_by
        // 		ORDER BY
        // 			cancelled_production_request.cpr_id
        // 		DESC
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