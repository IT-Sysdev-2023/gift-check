<?php

namespace App\Http\Controllers;

use App\Models\ApprovedGcrequest;
use App\Models\BudgetRequest;
use App\Models\StoreGcrequest;
use App\Models\User;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class Dashboard extends Controller
{
    public function index()
    {

        // dd(BudgetRequest::with('user')->get());

        // dd(request()->user()->usertype);

        // $bud = BudgetAdjustment::count();
        // $production = GcAdjustment::count();
        // $allocate = AllocationAdjustment::count();


        //BUDGET REQUEST

        $dept = request()->user();
        $data = $dept->load(['budgetRequest' => fn($query) => $query->where('br_request_status', 0)]);

        //Pending Request
        $budPenReq = $data->budgetRequest->count();

        //Approved Request
        $budAppReq = BudgetRequest::where('br_request_status', 1)->count();

        //Cancelled Request
        $budCanReq = BudgetRequest::where('br_request_status', 2)->count();


        //STORE GC REQUEST

        //Pending Request
        $storePenReq = StoreGcrequest::where(function (Builder $query) {
            $query->where('sgc_status', 0)
                ->orWhere('sgc_status', 1);
        })->where('sgc_cancel', '')->count();

        //Release Gc
        
        $gcRes = ApprovedGcrequest::with('storeGcRequest')->get();
        dd($gcRes);



    }

    // function GCReleasedAllStore($link)
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



    //     function getField($field,$table,$field2,$var)
// {
//     $query = $link->query("SELECT $field FROM $table WHERE $field2='$var'");
//     $row = $query->fetch_assoc();
//     return $row[$field];
// }
}
