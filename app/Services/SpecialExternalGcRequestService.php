<?php

namespace App\Services;

use App\Http\Resources\SpecialExternalGcRequestResource;
use App\Models\SpecialExternalGcrequest;
use App\Models\StoreGcrequest;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;

class SpecialExternalGcRequestService
{


    public static function approvedGc(): Collection //<a href="#/special-external-request-approved/">
    {
        $record = SpecialExternalGcrequest::joinSpecialExternalCustomer()
            ->select('approved_request.*', 'special_external_gcrequest.*')
            ->leftJoin('approved_request', function (JoinClause $join) {
                $join->on('special_external_gcrequest.spexgc_id', 'approved_request.reqap_trid')
                    ->where('approved_request.reqap_approvedtype', 'Special External GC Approved');
            })
            ->spexgcStatus('approved')->get();

        return $record;
    }

    public static function reviewedGc(): Collection //special-external-gc-reviewed
    {
        $data = SpecialExternalGcrequest::joinSpecialExternalCustomer()->with('user')
            ->withWhereHas(
                'approvedRequest',
                function ($query) {
                    $query->approvedType('Special External GC Approved');
                }
            )
            ->spexgcStatus('approved')->spexgcReviewed('reviewed')
            ->spexgcReleased('')->spexgcPromo('0')->oldest('spexgc_id')->get();
        return $data;

        // $table = 'special_external_gcrequest';
        // $select = "special_external_gcrequest.spexgc_num,
        //     special_external_gcrequest.spexgc_dateneed,
        //     special_external_gcrequest.spexgc_id,
        //     special_external_gcrequest.spexgc_datereq,
        //     CONCAT(users.firstname,' ',users.lastname) as prep,
        //     special_external_customer.spcus_acctname,
        //     special_external_customer.spcus_companyname,
        //     special_external_gcrequest.spexgc_id,
        //     approved_request.reqap_approvedby";
        // $where = "special_external_gcrequest.spexgc_status='approved'
        //     AND
        //         special_external_gcrequest.spexgc_reviewed='reviewed'
        //     AND
        //         approved_request.reqap_approvedtype='Special External GC Approved'
        //     AND
        //         special_external_gcrequest.spexgc_released=''
        //     AND 
        //         special_external_gcrequest.spexgc_promo = '0'";
        // $join = 'INNER JOIN
        //         users
        //     ON
        //         users.user_id = special_external_gcrequest.spexgc_reqby
        //     INNER JOIN
        //         special_external_customer
        //     ON
        //         special_external_customer.spcus_id = special_external_gcrequest.spexgc_company
        //     INNER JOIN
        //         approved_request
        //     ON
        //         approved_request.reqap_trid = special_external_gcrequest.spexgc_id';
        // $limit = 'ORDER BY special_external_gcrequest.spexgc_id ASC';

        // $request = getAllData($link,$table,$select,$where,$join,$limit);
    }


    public static function releasedGc(): Collection //released-special-external-request
    {
        $record = SpecialExternalGcrequest::joinSpecialExternalCustomer()->with('approvedRequest.user', 'user')
            ->withWhereHas('approvedRequest', function ($query) {
                $query->approvedType('special external releasing');
            })
            ->spexgcReleased('released')->get();

        // return SpecialExternalGcRequestResource::collection($record);
        return $record;
    }

    public static function cancelledGcRequest(): Collection //cancelled-gc-request.php
    {
        return StoreGcrequest::cancelledGcRequest()->get();

        // $rows = [];

		// $query = $link->query(
		// 	"SELECT 
		// 		`store_gcrequest`.`sgc_id`,
		// 		`store_gcrequest`.`sgc_num`,
		// 		`cancelled_store_gcrequest`.`csgr_by`,
		// 		`cancelled_store_gcrequest`.`csgr_at`,
		// 		`stores`.`store_name`,
		// 		`store_gcrequest`.`sgc_requested_by`,
		// 		`users`.`firstname`,
		// 		`users`.`lastname`,
		// 		`store_gcrequest`.`sgc_date_request`

		// 	FROM 
		// 		`store_gcrequest` 
		// 	INNER JOIN
		// 		`cancelled_store_gcrequest`
		// 	ON
		// 		`store_gcrequest`.`sgc_id` = `cancelled_store_gcrequest`.`csgr_gc_id`
		// 	INNER JOIN 
		// 		`stores`
		// 	ON
		// 		`store_gcrequest`.`sgc_store` = `stores`.`store_id`
		// 	INNER JOIN
		// 		`users`
		// 	ON
		// 		`store_gcrequest`.`sgc_requested_by` = `users`.`user_id`
		// 	WHERE 
		// 		`store_gcrequest`.`sgc_status`=0
		// 	AND
		// 		`store_gcrequest`.`sgc_cancel`='*'			
		// ");

		// if($query)
		// {
		// 	while($row = $query->fetch_object())
		// 	{
		// 		$rows[] = $row;
		// 	}

		// 	return $rows;
		// }
		// else 
		// {
		// 	return $rows[] = $link->error;
		// }
    }

    
}
