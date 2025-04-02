<?php

namespace App\Http\Controllers;

use App\Models\Assignatory;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


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
        $result = Customer::whereRaw("CONCAT(cus_lname, ' ', cus_fname, ' ', cus_mname, ' ', cus_namext) LIKE ?", ['%' . $request->search . '%'])
            ->get();
        if ($result->isEmpty()) {
            return response()->json([
                'success' => true,
                'data' => [],
                'message' => 'No customer found',
            ]);
        }
        $formattedResult = $result->map(function ($item) use ($request) {
            return [
                'value' => $item->cus_id,
                'label' => trim("{$item->cus_lname}, {$item->cus_fname} {$item->cus_mname} {$item->cus_namext}")
            ];
        });
        return response()->json([
            'success' => true,
            'data' => $formattedResult
        ]);
    }

    public function addCustomer(Request $request)
    {
        $request->validate([
            'firstname' => 'required',
            'lastname' => 'required',
        ]);
        try {
            $addCustomer = Customer::create([
                'cus_fname' => $request->firstname,
                'cus_lname' => $request->lastname,
                'cus_mname' => $request->middlename,
                'cus_namext' => $request->extention,
                'cus_store_register' => '1',
                'cus_register_by' => $request->user()->user_id,



            ]);
            return response()->json([
                'status' => 'Success',
                'message' => 'Customer added successfully',
                'data' => [
                    'cus_id' => $addCustomer->cus_id
                ]

            ]);
        } catch (\Exception $e) {
            Log::error('Error adding customer:' . $e->getMessage());

            return response()->json([
                'status' => 'Error',
                'message' => 'Failed to add customer',
            ], 500);
        }
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
