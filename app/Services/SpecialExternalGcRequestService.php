<?php

namespace App\Services;

use App\Models\SpecialExternalGcrequest;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\JoinClause;

class SpecialExternalGcRequestService
{


    public static function approvedGc(): Collection
    {
        $record = SpecialExternalGcrequest::with('specialExternalCustomer')->select('approved_request.*', 'special_external_gcrequest.*')
            ->leftJoin('approved_request', function (JoinClause $join) {
                $join->on('special_external_gcrequest.spexgc_id', 'approved_request.reqap_trid')
                    ->where('approved_request.reqap_approvedtype', 'Special External GC Approved');
            })
            ->spexgcStatus('approved')->get();

        return $record;
    }

    public static function reviewedGc()
    {
        SpecialExternalGcrequest::spexgcStatus('approved')->spexgcReviewed('reviewed')
            ->spexgcReleased('')->spexgcPromo('0');

    }

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