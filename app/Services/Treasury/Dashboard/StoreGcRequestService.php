<?php

namespace App\Services\Treasury\Dashboard;

use App\Http\Resources\ApprovedGcRequestResource;
use App\Http\Resources\StoreGcRequestResource;
use App\Models\ApprovedGcrequest;
use App\Models\GcRelease;
use App\Models\StoreGcrequest;
use App\Services\Treasury\ColumnHelper;
use App\Services\Treasury\Pdf\GcReleasedReport;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;

class StoreGcRequestService
{

	public function pendingRequest(Request $request) //tran_release_gc.php
	{
		$record = StoreGcrequest::pendingRequest()->paginate()->withQueryString();

		return inertia(
			'Treasury/StoreGcRequest/TableStoreGc',
			[
				'filters' => $request->all('search', 'date'),
				'title' => 'Pending Request',
				'data' => StoreGcRequestResource::collection($record),
				'columns' => ColumnHelper::$pendingStoreGcRequest,
			]

		);
	}

	public function releasedGc(Request $request) //approved-gc-request.php
	{
		$record = ApprovedGcrequest::with([
			'user:user_id,firstname,lastname',
			'storeGcRequest:sgc_id,sgc_store,sgc_date_request',
			'storeGcRequest.store:store_id,store_name'
		])
			->select('agcr_id', 'agcr_approved_at', 'agcr_approvedby', 'agcr_preparedby', 'agcr_rec', 'agcr_request_relnum', 'agcr_request_id')
			// ->withWhereHas('storeGcRequest.store')
			->orderByDesc('agcr_id')
			// ->limit(10)->get();
			->paginate()->withQueryString();

		return inertia(
			'Treasury/StoreGcRequest/TableStoreGc',
			[
				'filters' => $request->all('search', 'date'),
				'title' => 'Released Gc',
				'data' => ApprovedGcRequestResource::collection($record),
				'columns' => ColumnHelper::$releasedStoreGcRequest,
			]

		);
	}

	public static function cancelledRequest(): Collection
	{
		return StoreGcrequest::cancelledGcRequest()->get();
		//     function getAllCancelledGCRequestStore($link)
		// {
		// 	$rows = [];

		// 	$query = $link->query(
		// 		"SELECT 
		// 			`store_gcrequest`.`sgc_id`,
		// 			`store_gcrequest`.`sgc_num`,
		// 			`cancelled_store_gcrequest`.`csgr_by`,
		// 			`cancelled_store_gcrequest`.`csgr_at`,
		// 			`stores`.`store_name`,
		// 			`store_gcrequest`.`sgc_requested_by`,
		// 			`users`.`firstname`,
		// 			`users`.`lastname`,
		// 			`store_gcrequest`.`sgc_date_request`

		// 		FROM 
		// 			`store_gcrequest` 
		// 		INNER JOIN
		// 			`cancelled_store_gcrequest`
		// 		ON
		// 			`store_gcrequest`.`sgc_id` = `cancelled_store_gcrequest`.`csgr_gc_id`
		// 		INNER JOIN 
		// 			`stores`
		// 		ON
		// 			`store_gcrequest`.`sgc_store` = `stores`.`store_id`
		// 		INNER JOIN
		// 			`users`
		// 		ON
		// 			`store_gcrequest`.`sgc_requested_by` = `users`.`user_id`
		// 		WHERE 
		// 			`store_gcrequest`.`sgc_status`=0
		// 		AND
		// 			`store_gcrequest`.`sgc_cancel`='*'			
		// 	");

		// 	if($query)
		// 	{
		// 		while($row = $query->fetch_object())
		// 		{
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

	public function reprint($id)
	{

		$record = GcRelease::where('rel_num', $id)->exists();
		if (!$record) {
			return response()->json(['error' => 'Pdf Not available'], 404);
		}
		$data = [
			[
				'quantity' => 1,
				'description' => '1 Year Subscription',
				'price' => '129.00'
			]
		];

		$d = GcRelease::selectRaw('IFNULL(SUM(denomination.denomination), 0.00) as total, IFNULL(COUNT(gc_release.re_barcode_no), 0) as cnt')
			->joinGcDenomination()
			->where('gc_release.rel_num', $id)
			->first();

		$result_header_data = ApprovedGcrequest::where('agcr_id', $id)->with(['storeGcRequest.store:store_id,store_name', 'user:user_id,firstname,lastname'])->first();
		$header_data = new ApprovedGcRequestResource($result_header_data);
		$gcgroup = GcRelease::select('denomination.denomination', 'gc_release.re_barcode_no', 'gc_release.rel_id', 'denomination.denom_id')
			->joinGcDenomination()
			->where('rel_num', $id)
			->first();

		// $data = (new GcReleasedReport())
		// 		->releasedDate()

		dd($header_data->agcr_approved_at);
		// dd(response()->json(new ApprovedGcRequestResource($header_data)));

		$pdf = Pdf::loadView('pdf.storegc', ['data' => $data]);
		$pdf->setPaper('A3');

		$pdfContent = $pdf->output();

		return response($pdfContent, 200)
			->header('Content-Type', 'application/pdf');
		// ->header('Content-Disposition', 'inline; filename="sample.pdf"');
	}


	// $rows = [];
	// $query = $link->query(
	// 	"SELECT 
	// 		`denomination`.`denomination`,
	// 		`gc_release`.`re_barcode_no`,
	// 		`gc_release`.`rel_id`,
	// 		`denomination`.`denom_id`
	// 	FROM 
	// 		`gc_release`
	// 	INNER JOIN
	// 		`gc`
	// 	ON
	// 		`gc`.`barcode_no` = `gc_release`.`re_barcode_no`
	// 	INNER JOIN
	// 		`denomination`
	// 	ON
	// 		`denomination`.`denom_id` = `gc`.`denom_id`
	// 	WHERE 
	// 		`gc_release`.`rel_num`='$id'
	// 	GROUP BY
	// 		`denomination`.`denomination`
	// 	ORDER BY
	// 		`denomination`.`denom_id`
	// ");

}