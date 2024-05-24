<?php

namespace App\Services;
use App\Models\ApprovedGcrequest;
use App\Models\StoreGcrequest;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class StoreGcRequestService
{

    public static function pendingRequest(): Collection //tran_release_gc.php
    {
        $record = StoreGcrequest::withWhereHas('user')->withWhereHas('store')
            ->where(function (Builder $query){
                $query->where('sgc_status', 1)
                    ->orWhere('sgc_status', 0);
            })
            ->where('sgc_cancel', '')
            ->orderByDesc('sgc_id')->get();

        return $record;
    //     function gcstorerequestList($link)
	// {
	// 	$rows = [];
	//     $query = $link->query(
	//         "SELECT
	// 		    `store_gcrequest`.`sgc_id`,
	// 		    `store_gcrequest`.`sgc_num`,
	// 		    `store_gcrequest`.`sgc_date_needed`,
	// 		    `store_gcrequest`.`sgc_date_request`,
	// 		    `store_gcrequest`.`sgc_status`,
	// 		    `stores`.`store_name`,
	// 			`users`.`firstname`,
	// 			`users`.`lastname`
	// 		FROM 
	// 			`store_gcrequest`
	// 		INNER JOIN
	// 			`stores`
	// 		ON
	// 			`store_gcrequest`.`sgc_store` = `stores`.`store_id`
	// 		INNER JOIN
	// 			`users`
	// 		ON
	// 			`users`.`user_id` = `store_gcrequest`.`sgc_requested_by`
	// 		WHERE
	// 			`store_gcrequest`.`sgc_status` = 1
	// 		OR
	// 			`store_gcrequest`.`sgc_status` = 0
	// 		AND
	// 			`store_gcrequest`.`sgc_cancel`=''
	// 		ORDER BY
	// 			`store_gcrequest`.`sgc_id`
	// 		DESC
	//     ");

	//     if($query)
	//     {
	// 	    while ($row = $query->fetch_object()) {
	// 	    	$rows[] = $row;
	// 	    }
	// 	    return $rows;
	//     }
	//     else 
	//     {
	//     	return $rows[] = $link->error;
	//     }
	// }
    }

    public static function releasedGc(): Collection //approved-gc-request.php
    {
        $record = ApprovedGcrequest::with('user')->withWhereHas('storeGcRequest.store')
                    ->orderByDesc('agcr_id')->get();

        return $record;

    //     function GCReleasedAllStore($link)
	// {
	// 	$rows = [];
	// 	$query = $link->query(
	// 	"SELECT
	// 		`approved_gcrequest`.`agcr_id`,
	// 		`stores`.`store_name`,
	// 		`approved_gcrequest`.`agcr_approved_at`,
	// 		`approved_gcrequest`.`agcr_approvedby`,
	// 		`approved_gcrequest`.`agcr_preparedby`,
	// 		`approved_gcrequest`.`agcr_rec`,
	// 		`approved_gcrequest`.`agcr_request_relnum`,
	// 		`agcr_request_relnum`,
	// 		`users`.`firstname`,
	// 		`users`.`lastname`,
	// 		`store_gcrequest`.`sgc_date_request`
	// 	FROM
	// 		`approved_gcrequest`
	// 	INNER JOIN
	// 		`store_gcrequest`
	// 	ON
	// 		`approved_gcrequest`.`agcr_request_id` = `store_gcrequest`.`sgc_id`
	// 	INNER JOIN
	// 		`stores`
	// 	ON
	// 		`store_gcrequest`.`sgc_store` = `stores`.`store_id`
	// 	INNER JOIN
	// 		`users`
	// 	ON
	// 		`approved_gcrequest`.`agcr_preparedby` = `users`.`user_id`
	// 	ORDER BY
	// 		`approved_gcrequest`.`agcr_id`
	// 	DESC
	// 	");

	// 	if($query)
	// 	{
	// 		while ($row = $query->fetch_object()) {
	// 			$rows[] = $row;
	// 		}
	// 		return $rows;
	// 	}
	// 	else
	// 	{
	// 		return $rows[] = $link->error;
	// 	}
	// }
    }

    public static function cancelledRequest(){



    //     function getAllCancelledGCRequestStore($link)
	// {
	// 	$rows = [];

	// 	$query = $link->query(
	// 		"SELECT 
	// 			`store_gcrequest`.`sgc_id`,
	// 			`store_gcrequest`.`sgc_num`,
	// 			`cancelled_store_gcrequest`.`csgr_by`,
	// 			`cancelled_store_gcrequest`.`csgr_at`,
	// 			`stores`.`store_name`,
	// 			`store_gcrequest`.`sgc_requested_by`,
	// 			`users`.`firstname`,
	// 			`users`.`lastname`,
	// 			`store_gcrequest`.`sgc_date_request`

	// 		FROM 
	// 			`store_gcrequest` 
	// 		INNER JOIN
	// 			`cancelled_store_gcrequest`
	// 		ON
	// 			`store_gcrequest`.`sgc_id` = `cancelled_store_gcrequest`.`csgr_gc_id`
	// 		INNER JOIN 
	// 			`stores`
	// 		ON
	// 			`store_gcrequest`.`sgc_store` = `stores`.`store_id`
	// 		INNER JOIN
	// 			`users`
	// 		ON
	// 			`store_gcrequest`.`sgc_requested_by` = `users`.`user_id`
	// 		WHERE 
	// 			`store_gcrequest`.`sgc_status`=0
	// 		AND
	// 			`store_gcrequest`.`sgc_cancel`='*'			
	// 	");

	// 	if($query)
	// 	{
	// 		while($row = $query->fetch_object())
	// 		{
	// 			$rows[] = $row;
	// 		}

	// 		return $rows;
	// 	}
	// 	else 
	// 	{
	// 		return $rows[] = $link->error;
	// 	}

	// }
    }
}