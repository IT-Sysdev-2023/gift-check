<?php

namespace App\Services;
use App\Models\LedgerBudget;
use App\Models\LedgerCheck;

class LedgerService
{
    public static function budgetLedger() //ledger_budget.php
	{

		return LedgerBudget::select('bledger_id', 'bledger_no', 'bledger_trid', 'bledger_datetime', 'bledger_type', 'bdebit_amt', 'bcredit_amt')->get();
		// $rows = [];
		// $query = $link->query(
		// 	"SELECT 
		// 		ledger_budget.bledger_id,
		// 		ledger_budget.bledger_no,
		// 		ledger_budget.bledger_trid,
		// 		ledger_budget.bledger_datetime,
		// 		ledger_budget.bledger_type,
		// 		ledger_budget.bdebit_amt,
		// 		ledger_budget.bcredit_amt
		// 	FROM 
		// 		ledger_budget 
		// ");

		// if($query)
		// {
		// 	while ($row = $query->fetch_object()) {
		// 		$rows[] = $row;
		// 	}
		// 	return $rows;
		// }
		// else 
		// 	return $rows[] = $link->error;
	}
	public static function gcLedger() // gccheckledger.php
	{

		$record = LedgerCheck::with('user:user_id,firstname,lastname')
			->select(
				'cledger_id',
				'c_posted_by',
				'cledger_no',
				'cledger_datetime',
				'cledger_type',
				'cledger_desc',
				'cdebit_amt',
				'ccredit_amt',
				'c_posted_by'
			)
			->orderBy('cledger_id')
			->cursorPaginate()
			->withQueryString();
		return $record;
		// $rows = [];
		// $query = $link->query(
		// 	"SELECT
		// 		`ledger_check`.`cledger_no`,
		// 		`ledger_check`.`cledger_datetime`,
		// 		`ledger_check`.`cledger_type`,
		// 		`ledger_check`.`cledger_desc`,
		// 		`ledger_check`.`cdebit_amt`,
		// 		`ledger_check`.`ccredit_amt`,
		// 		`ledger_check`.`c_posted_by`,
		// 		`users`.`firstname`,
		// 		`users`.`lastname`
		// 	FROM 
		// 		`ledger_check`
		// 	INNER JOIN
		// 		`users`
		// 	ON
		// 		`users`.`user_id` = `ledger_check`.`c_posted_by`
		// 	ORDER BY
		// 		`ledger_check`.`cledger_id`
		// 	ASC 
		// ");

		// if($query)
		// {
		// 	while ($row = $query->fetch_object()) 
		// 	{
		// 		$rows[] = $row;
		// 	}
		// 	return $rows;
		// }
		// else 
		// {
		// 	return $link->error;
		// }
	}
}