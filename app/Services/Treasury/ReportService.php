<?php

namespace App\Services\Treasury;

use App\Models\LedgerBudget;
use App\Models\LedgerCheck;
use App\Models\Store;

class ReportService
{
	public static function reports() //storesalesreport.php
	{

		$record = Store::select('store_id', 'store_name', 'default_password', 'company_code', 'store_code', 'issuereceipt')
			->where('store_status', 'active')
			->cursorPaginate()
			->withQueryString();

		return $record;

		// $rows = [];
		// $query = $link->query(
		// 	"SELECT 
		// 		store_id, 
		// 		store_name,
		// 		default_password,
		// 		company_code,
		// 		store_code,
		// 		issuereceipt 
		// 	FROM 
		// 		stores
		// 	WHERE
		// 		store_status='active'
		// "
		// );

		// if ($query) {
		// 	while ($row = $query->fetch_object()) {
		// 		$rows[] = $row;
		// 	}
		// 	return $rows;
		// } else {
		// 	return $rows[] = $link->query;
		// }
	}

}