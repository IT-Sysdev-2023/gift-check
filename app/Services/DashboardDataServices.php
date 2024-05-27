<?php

namespace App\Services;

use App\Models\AllocationAdjustment;
use App\Models\InstitutEod;
use App\Models\InstitutTransaction;
use App\Models\LedgerBudget;
use App\Models\ProductionRequest;
use App\Models\PromoGcReleaseToDetail;
use Illuminate\Database\Eloquent\Collection;

class DashboardDataServices
{
    public function promoGcReleased() // #/promo-gc-released-list
    {
        $record = PromoGcReleaseToDetail::with([
            'user:user_id,firstname,lastname',
            'promoGcRequest:pgcreq_id,pgcreq_reqnum'
        ])
            // ->join('users', 'promo_gc_release_to_details.prrelto_relby', '=', 'users.user_id')
            // ->join('promo_gc_request', 'promo_gc_release_to_details.prrelto_trid', '=', 'promo_gc_request.pgcreq_id')
            ->select('prrelto_id', 'prrelto_relnumber', 'prrelto_trid', 'prrelto_docs', 'prrelto_checkedby', 'prrelto_approvedby', 'prrelto_date', 'prrelto_recby', 'prrelto_status')
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
        $record = InstitutTransaction::leftJoin('institut_customer', 'institut_transactions.institutr_cusid', '=', 'institut_customer.ins_id')
            ->select('institutr_id', 'institutr_trnum', 'institutr_paymenttype', 'institutr_receivedby', 'institutr_date', 'ins_name')
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
        $record = LedgerBudget::with([
            'budgetAdjustment.user:user_id,firstname,lastname',
            'budgetAdjustment:bud_id,bud_by,bud_adj_type,bud_remark'
        ])
            // ->join('budget_adjustment', 'ledger_budget.bledger_trid', '=', 'budget_adjustment.bud_id')
            ->select('bledger_datetime', 'bdebit_amt', 'bcredit_amt')
            ->where('bledger_type', 'BA')
            ->get();
        return $record;

        //     function getAdjBudget($link)
        // {
        // 	$rows = [];
        // 	$query = $link->query(
        // 		"SELECT 
        // 			budget_adjustment.bud_adj_type,
        // 			budget_adjustment.bud_remark,
        // 			ledger_budget.bledger_datetime,
        // 			ledger_budget.bdebit_amt,
        // 			ledger_budget.bcredit_amt,
        // 			CONCAT(users.firstname,' ',users.lastname) as prepby
        // 		FROM 
        // 			ledger_budget
        // 		INNER JOIN
        // 			budget_adjustment
        // 		ON
        // 			budget_adjustment.bud_id = ledger_budget.bledger_trid
        // 		INNER JOIN
        // 			users
        // 		ON
        // 			users.user_id = budget_adjustment.bud_by
        // 		WHERE 
        // 			ledger_budget.bledger_type='BA'
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

    public function allocationAdjustments() //view-allocation-adj.php
    {
        $record = AllocationAdjustment::with([
            'user:user_id,firstname,lastname',
            'store:store_id,store_name',
            'gcType:gc_type_id,gctype'
        ])
            ->select('aadj_id', 'aadj_datetime', 'aadj_type', 'aadj_remark', 'aadj_loc', 'aadj_gctype')
            // ->join('stores', 'allocation_adjustment.aadj_loc', '=', 'stores.store_id')
            // ->join('gc_type', 'allocation_adjustment.aadj_gctype', '=', 'gc_type.gc_type_id')
            ->get();

        return $record;

        // $table = allocation_adjustment
        //     $select = 'allocation_adjustment.aadj_id,
        //     allocation_adjustment.aadj_datetime,
        //     allocation_adjustment.aadj_type,
        //     stores.store_name,
        //     gc_type.gctype,
        //     users.firstname,
        //     users.lastname,
        //     allocation_adjustment.aadj_remark';
        // $join = 'INNER JOIN
        //         stores
        //     ON
        //         stores.store_id =  allocation_adjustment.aadj_loc
        //     INNER JOIN
        //         gc_type
        //     ON
        //         gc_type.gc_type_id = allocation_adjustment.aadj_gctype
        //     INNER JOIN
        //         users
        //     ON
        //         users.user_id = allocation_adjustment.aadj_by';

    }

    public function eodList() // #/eod-list/
    {
        $record = InstitutEod::with('user:user_id,firstname,lastname')
            ->select('ieod_id', 'ieod_by', 'ieod_num', 'ieod_date')
            ->orderByDesc('ieod_date')
            ->get();
        return $record;

        // $where = "1";
        // $select = "	institut_eod.ieod_id,
        //     institut_eod.ieod_num,
        //     institut_eod.ieod_date,
        //     CONCAT(users.firstname,' ',users.lastname) as eodby";
        // $join = "INNER JOIN
        //         users
        //     ON
        //         users.user_id = institut_eod.ieod_by";
        // $limit ='ORDER BY institut_eod.ieod_date DESC';
        // $data = getAllData($link,'institut_eod',$select,$where,$join,$limit); 

    }

    public function productionRequest()
    {
        $record = ProductionRequest::select('pe_id', 'pe_num')
            ->where([['pe_generate_code', '0'], ['pe_status', '1']])
            ->get();
        return $record;
    }
}