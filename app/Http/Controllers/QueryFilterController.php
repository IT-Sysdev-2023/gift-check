<?php

namespace App\Http\Controllers;

use App\Models\Assignatory;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QueryFilterController extends Controller
{

    public function getCheckBy(Request $request)
    {
        return response()->json(
            Assignatory::whereIn('assig_dept', [$request->user()->usertype, 1])->get()
        );
    }
    public function customer(Request $request)
    {
        $result = Customer::whereAny(['cus_fname', 'cus_lname'], 'LIKE', '%' . $request->search . '%')->get();

        return response()->json($result);
    }

    // $query =  $link->query("SELECT
    // 		CONCAT(cus_fname,' ',cus_mname,' ',cus_lname,' ',cus_namext) as name,
    // 		cus_id,
    // 		cus_fname,
    // 		cus_mname,
    // 		cus_lname,
    // 		cus_namext
    // 	FROM
    // 		customers
    // 	WHERE
    // 		CONCAT(cus_fname,' ', cus_lname) LIKE '%" . $cust . "%' OR CONCAT(cus_fname,' ',cus_mname,' ', cus_lname) LIKE '%" . $cust . "%' LIMIT 8
    // 	");

    // 				if ($query->num_rows > 0) {
    // 					$html = "<ul>";
    // 					while ($row = $query->fetch_object()) {
    // 						$fullname = "";
    // 						$fullname .= $row->cus_fname;
    // 						if (!empty($row->cus_mname)) {

    // 							$fullname .= ' ' . $row->cus_mname;
    // 						}

    // 						$fullname .= ' ' . $row->cus_lname;
    // 						if (!empty($row->cus_namext)) {
    // 							$fullname .= ' ' . $row->cus_namext;
    // 						}
    // 						$html .= "<li class='vernames' data-id='" . $row->cus_id . "' data-fname='" . $row->cus_fname . "' data-mname='" . $row->cus_mname . "' data-lname='" . $row->cus_lname . "' data-namext='" . $row->cus_namext . "'>" . $fullname . "</li>";
    // 					}
    // 					$html .= "</ul>";
    // 					$response['st'] = true;
    // 					$response['msg'] = $html;
    // 				} else {
    // 					$response['msg'] = '<div class="_emptyresajax">No Result Found.<div>';
    // 				}
}
