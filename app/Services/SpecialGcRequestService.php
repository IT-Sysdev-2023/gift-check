<?php

namespace App\Services;

use App\Models\SpecialExternalGcrequest;
use App\Models\StoreGcrequest;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Contracts\Database\Eloquent\Builder;

class SpecialGcRequestService
{
    public function pendingRequest() // #/special-external-request
    {

        $request = self::forPendingRequest('0');

        $request2 = self::forPendingRequest('*');

        //     $table = 'special_external_gcrequest';
        // $select = " special_external_gcrequest.spexgc_num,
        //     special_external_gcrequest.spexgc_dateneed,
        //     special_external_gcrequest.spexgc_id,
        //     special_external_gcrequest.spexgc_datereq,
        //     CONCAT(users.firstname,' ',users.lastname) as prep,
        //     special_external_customer.spcus_acctname,
        //     special_external_customer.spcus_companyname";
        // $where = "special_external_gcrequest.spexgc_status='pending' AND special_external_gcrequest.spexgc_promo = '0'";
        // $join = 'INNER JOIN
        //         users
        //     ON  
        //         users.user_id = special_external_gcrequest.spexgc_reqby
        //     INNER JOIN
        //         special_external_customer
        //     ON
        //         special_external_customer.spcus_id = special_external_gcrequest.spexgc_company';
        // $limit = 'ORDER BY special_external_gcrequest.spexgc_id ASC';

        // $request = getAllData($link,$table,$select,$where,$join,$limit);

        // $table2 = 'special_external_gcrequest';
        // $select2 = " special_external_gcrequest.spexgc_num,
        //     special_external_gcrequest.spexgc_dateneed,
        //     special_external_gcrequest.spexgc_id,
        //     special_external_gcrequest.spexgc_datereq,
        //     CONCAT(users.firstname,' ',users.lastname) as prep,
        //     special_external_customer.spcus_acctname,
        //     special_external_customer.spcus_companyname";
        // $where2 = "special_external_gcrequest.spexgc_status='pending' AND special_external_gcrequest.spexgc_promo = '*'";
        // $join2 = 'INNER JOIN
        //         users
        //     ON
        //         users.user_id = special_external_gcrequest.spexgc_reqby
        //     INNER JOIN
        //         special_external_customer
        //     ON
        //         special_external_customer.spcus_id = special_external_gcrequest.spexgc_company';
        // $limit2 = 'ORDER BY special_external_gcrequest.spexgc_id ASC';

        // $request2 = getAllData($link,$table2,$select2,$where2,$join2,$limit2);

    }
    public function approvedGc() // #/special-external-request-approved
    {

        $record = SpecialExternalGcrequest::joinSpecialExternalCustomer()
            ->leftJoin('approved_request', function (JoinClause $join) {
                $join->on('special_external_gcrequest.spexgc_id', 'approved_request.reqap_trid')
                    ->select('reqap_approvedby', 'reqap_date', 'reqap_approvedtype')
                    ->where('approved_request.reqap_approvedtype', 'Special External GC Approved');
            })
            ->spexgcStatus('approved')
            ->select('spexgc_id', 'spexgc_num', 'spexgc_datereq', 'spexgc_dateneed', 'spcus_acctname', 'spcus_companyname')
            ->get();

        return $record;

        // $table = 'special_external_gcrequest';
        // $select = 'special_external_gcrequest.spexgc_id,
        //     special_external_gcrequest.spexgc_num,
        //     special_external_gcrequest.spexgc_datereq,
        //     special_external_gcrequest.spexgc_dateneed,
        //     approved_request.reqap_approvedby,
        //     approved_request.reqap_date,
        //     special_external_customer.spcus_acctname,
        //     special_external_customer.spcus_companyname';
        // $where = "special_external_gcrequest.spexgc_status='approved' AND
        //         approved_request.reqap_approvedtype = 'Special External GC Approved'";

        // $join = 'INNER JOIN
        //         special_external_customer
        //     ON
        //         special_external_customer.spcus_id = special_external_gcrequest.spexgc_company
        //     LEFT JOIN
        //         approved_request
        //     ON
        //         approved_request.reqap_trid = special_external_gcrequest.spexgc_id';
        // $limit ='';
        // $data = getAllData($link,$table,$select,$where,$join,$limit);


    }
    public function reviewedGcForReleasing() // #/special-external-gc-reviewed
    {

        $record = SpecialExternalGcrequest::joinSpecialExternalCustomer()
            ->with([
                'user:user_id,firstname,lastname',
                'approvedRequest' => function (Builder $query) {
                    $query->select('reqap_trid', 'reqap_approvedtype', 'reqap_approvedby')
                        ->approvedType('Special External GC Approved');
                }
            ])
            // ->join('approved_request', function (JoinClause $join) {
            //     $join->on('special_external_gcrequest.spexgc_id', '=', 'approved_request.reqap_trid')
            //         ->where('approved_request.reqap_approvedtype', 'Special External GC Approved');
            // })
            ->select('spcus_acctname', 'spcus_companyname', 'spexgc_num', 'spexgc_dateneed', 'spexgc_id', 'spexgc_datereq')
            ->spexgcStatus('approved')
            ->spexgcReviewed('reviewed')
            ->spexgcReleased('')
            ->spexgcPromo('0')
            // ->with('user')
            ->orderBy('spexgc_id')
            ->get();
        return $record;

        //     $table = 'special_external_gcrequest';
        // $select = "special_external_gcrequest.spexgc_num,
        //     special_external_gcrequest.spexgc_dateneed,
        //     special_external_gcrequest.spexgc_id,
        //     special_external_gcrequest.spexgc_datereq,
        //     CONCAT(users.firstname,' ',users.lastname) as prep,
        //     special_external_customer.spcus_acctname,
        //     special_external_customer.spcus_companyname,
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
    public function releasedGc() //released-special-external-request
    {

        $record = SpecialExternalGcrequest::releasedGc()->get();
        return $record;

        //     $table='special_external_gcrequest';
        // $select ="special_external_gcrequest.spexgc_id,

        //     special_external_gcrequest.spexgc_num,
        //     CONCAT(req.firstname,' ',req.lastname) as reqby,
        //     special_external_gcrequest.spexgc_datereq,
        //     special_external_gcrequest.spexgc_dateneed,
        //     special_external_customer.spcus_acctname,
        //     special_external_customer.spcus_companyname,
        //     approved_request.reqap_date,
        // 	    CONCAT(rev.firstname,' ',rev.lastname) as revby";
        // $where = "special_external_gcrequest.spexgc_released='released'
        // 	AND
        // 		approved_request.reqap_approvedtype='special external releasing'";
        // $join = 'INNER JOIN
        // 		users as req
        // 	ON
        // 		req.user_id = special_external_gcrequest.spexgc_reqby
        // 	INNER JOIN
        // 		special_external_customer
        // 	ON
        // 		special_external_customer.spcus_id = special_external_gcrequest.spexgc_company  
        // 	INNER JOIN
        // 		approved_request
        // 	ON
        // 		approved_request.reqap_trid = special_external_gcrequest.spexgc_id
        // 	INNER JOIN
        // 		users as rev
        // 	ON
        // 		rev.user_id = approved_request.reqap_preparedby';
        // $limit = '';

        // $data = getAllData($link,$table,$select,$where,$join,$limit);

    }
    public function cancelledRequest() //cancelled-gc-request.php
    {
        $record = StoreGcrequest::with([
            'cancelledStoreGcRequest:csgr_id,csgr_gc_id,csgr_by,csgr_at',
            'store:store_id,store_name',
            'user:user_id,firstname,lastname'
        ])
            ->select('sgc_id', 'sgc_num', 'sgc_requested_by', 'sgc_date_request')
            ->where([['sgc_status', 0], ['sgc_cancel', '*']])
            ->get();


        return $record;
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

    private static function forPendingRequest(string $param)
    {
        return SpecialExternalGcrequest::joinSpecialExternalCustomer()
            ->with('user:user_id,firstname,lastname')
            ->select('spexgc_num', 'spexgc_dateneed', 'spexgc_id', 'spexgc_datereq', 'spcus_acctname', 'spcus_companyname')
            ->spexgcStatus('pending')
            ->spexgcPromo($param)
            ->orderBy('spexgc_id')
            ->get();
    }

}