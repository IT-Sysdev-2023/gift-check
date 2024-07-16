<?php

namespace App\Services\Treasury\Dashboard;

use App\Models\CancelledProductionRequest;
use App\Models\Denomination;
use App\Models\ProductionRequest;

class GcProductionRequestService
{
    public function pendingRequest() //pending_production_request.php
    {

        $dept = request()->user()->usertype;

        $pr = ProductionRequest::withWhereHas('user', fn($query) => $query->select('user_id', 'firstname', 'lastname')->where('usertype', $dept))
            ->select('pe_id', 'pe_file_docno', 'pe_date_needed', 'pe_remarks', 'pe_num', 'pe_date_request', 'pe_group')
            ->where('pe_status', 0)
            ->orderByDesc('pe_id')
            ->first();
        $denoms = Denomination::denomation();
    }

    public function approvedRequest() //approved-production-request
    {

        return ProductionRequest::with([
            'user:user_id,firstname,lastname',
            'approvedProductionRequest:ape_id,ape_pro_request_id,ape_approved_at,ape_approved_by'
        ])
            ->select('pe_id', 'pe_requested_by', 'pe_num', 'pe_date_request', 'pe_date_needed')
            ->where('pe_status', 1)
            ->orderByDesc('pe_id')
            ->paginate(10)
            ->withQueryString();
    }

    public function cancelledRequest() // cancelled-production-request.php
    {

        $record = CancelledProductionRequest::join('production_request', 'cancelled_production_request.cpr_pro_id', '=', 'production_request.pe_id')
            ->join('users as lreq', 'cancelled_production_request.pe_requested_by', '=', 'lreq.user_id')
            ->join('users as lcan', 'cancelled_production_request.cpr_by', '=', 'lcan.user_id')
            ->select('pe_id', 'pe_num', 'pe_date_request', 'pe_date_needed', 'lreq.firstname as lreqfname', 'lreq.lastname as lreqlname', 'cpr_at', 'lcan.firstname as lcanfname', 'lcan.lastname as lcanlname')
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