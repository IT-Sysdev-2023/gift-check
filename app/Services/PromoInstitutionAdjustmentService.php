<?php

namespace App\Services;

use App\Models\InstitutTransaction;
use App\Models\PromoGcReleaseToDetail;
use Illuminate\Database\Eloquent\Collection;

class PromoInstitutionAdjustmentService
{

    public function promoGcReleased() // #/promo-gc-released-list
    {
        $record = PromoGcReleaseToDetail::join('users', 'prrelto_relby', 'user_id')
            ->join('promo_gc_request', 'prrelto_trid', 'pgcreq_id')
            ->orderByDesc('prrelto_id')
            ->get();
        return $record;

        //     $table = 'promo_gc_release_to_details';
        // $select = "promo_gc_release_to_details.prrelto_id,
        //     promo_gc_release_to_details.prrelto_relnumber,
        //     promo_gc_release_to_details.prrelto_trid,
        //     promo_gc_release_to_details.prrelto_docs,
        //     promo_gc_release_to_details.prrelto_checkedby,
        //     promo_gc_release_to_details.prrelto_approvedby,
        //     promo_gc_release_to_details.prrelto_date,
        //     promo_gc_release_to_details.prrelto_recby,
        //     promo_gc_release_to_details.prrelto_status,
        //     CONCAT(users.firstname,' ',users.lastname) as relby,
        //     promo_gc_request.pgcreq_reqnum";
        // $where = '1';
        // $join = 'INNER JOIN
        //         users
        //     ON
        //         users.user_id = promo_gc_release_to_details.prrelto_relby
        //     INNER JOIN
        //         promo_gc_request
        //     ON
        //         promo_gc_request.pgcreq_id = promo_gc_release_to_details.prrelto_trid'; 
        // $limit = 'ORDER BY
        //         promo_gc_release_to_details.prrelto_id
        //     DESC';
        // $data = getAllData($link,$table,$select,$where,$join,$limit);
    }

    public function institutionGcSales(): Collection // #/institution-gc-sales
    {
        $record = InstitutTransaction::leftJoin('institut_customer', 'institutr_cusid', 'ins_id')
            // ->where("1")
            ->get();

        return $record;
        //     $table = 'institut_transactions';
        // $select = 'institut_transactions.institutr_id,
        // 	institut_transactions.institutr_trnum,
        // 	institut_transactions.institutr_paymenttype,
        // 	institut_transactions.institutr_receivedby,
        // 	institut_transactions.institutr_date,
        // 	institut_customer.ins_name';
        // $where = "1";
        // $join = 'LEFT JOIN
        // 		institut_customer
        // 	ON
        // 		institut_customer.ins_id = institut_transactions.institutr_cusid';
        // $limit = '';

        // $transactions = getAllData($link,$table,$select,$where,$join,$limit);
    }

    //ADJUSTMENTS
    public function budgetAdjustments() //view-budget-adj.php
    {

    }

    public function allocationAdjustments(){

    }

    
}