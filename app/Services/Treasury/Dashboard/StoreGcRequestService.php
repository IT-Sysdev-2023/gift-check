<?php

namespace App\Services\Treasury\Dashboard;

use App\Helpers\NumberHelper;
use App\Http\Resources\ApprovedGcRequestResource;
use App\Http\Resources\StoreGcRequestResource;
use App\Models\ApprovedGcrequest;
use App\Models\GcRelease;
use App\Models\InstitutPayment;
use App\Models\StoreGcrequest;
use App\Services\Treasury\ColumnHelper;
use App\Services\Treasury\Pdf\GcReleasedReport;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Date;

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
			->orderByDesc('agcr_id')
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
		if (!GcRelease::where('rel_num', $id)->exists()) {
			return response()->json(['error' => 'Pdf Not available'], 404);
		}

		$total_gc = GcRelease::selectRaw('IFNULL(SUM(denomination.denomination), 0.00) as total, IFNULL(COUNT(gc_release.re_barcode_no), 0) as cnt')
			->joinGcDenomination()
			->where('gc_release.rel_num', $id)
			->first();

		$header = ApprovedGcrequest::where('agcr_request_relnum', $id)->with(['storeGcRequest:sgc_id,sgc_store', 'storeGcRequest.store:store_id,store_name', 'user:user_id,firstname,lastname'])->first();
		$header_data = new ApprovedGcRequestResource($header);

		$gcgroup = GcRelease::select('denomination.denomination', 'gc_release.re_barcode_no', 'gc_release.rel_id', 'denomination.denom_id')
			->joinGcDenomination()
			->where('rel_num', $id)
			->get();

		if (!empty($header_data->agcr_paymenttype)) {

			$payment = InstitutPayment::select('insp_trid', 'insp_paymentcustomer', 'institut_bankname', 'institut_bankaccountnum', 'institut_checknumber', 'institut_amountrec', 'institut_jvcustomer')->where([['insp_trid', $id + 2], ['insp_paymentcustomer', 'stores']])->first();
			if ($header_data->agcr_paymenttype == 'cash') {
				$amountReceive = NumberHelper::format($payment->institut_amountrec);
			}

			if ($header_data->agcr_paymenttype == 'check') {
				$bankName = Str::upper($payment->institut_bankname);
				$bankAccount = Str::upper($payment->institut_bankaccountnum);
				$check = Str::upper($payment->institut_checknumber);
				$checkAmmount = NumberHelper::format($payment->institut_amountrec);
			}

			if ($header_data->agcr_paymenttype == 'jv') {
				$customer = Str::upper($payment->institut_jvcustomer);
			}
		}

		$data = [
			//Header
			'company' => [
				'name' => Str::upper('ALTURAS GROUP OF COMPANIES'),
				'department' => Str::title('Head Office - Treasury Department'),
				'report' => 'GC Releasing Report',
				'location' => 'AGC Head Office',
			],

			//SubHeader
			'gc_rel_no' => $id,
			'store' => $header_data->storeGcRequest?->store?->store_name,
			'date_released' => Date::parse($header_data->agcr_approved_at)->toFormattedDateString(),

			//Data/ Barcodes
			'barcode' => $gcgroup->groupBy('denomination')->map(fn($d) => $d->sortBy('denom_id')),
			
			//Subfooter
			'releasing_type' => self::releasingType($header_data->agcr_stat),
			'total_number_of_gc' => NumberHelper::format($total_gc->cnt),
			'total_gc_amount' => $total_gc->total,
			'payment_type' => Str::upper($header_data->agcr_paymenttype),

			'amount_receive' => $amountReceive ?? null,
			'bank_name' => $bankName ?? null,
			'bank_account' => $bankAccount ?? null,
			'check' => $check ?? null,
			'check_amount' => $checkAmmount ?? null,
			'customer' => $customer ?? null,

			//Signatures
			'received_by' => $header_data->agcr_recby,
			'released_by' => Str::upper($header_data->user->fullname),
			'checked_by' => Str::upper($header_data->agcr_checkedby),
		];

		$pdf = Pdf::loadView('pdf.storegc', ['data' => $data]);
		$pdf->setPaper('A3');

		$pdfContent = $pdf->output();

		return response($pdfContent, 200)->header('Content-Type', 'application/pdf');
	}

	public static function releasingType(int $rt){
		$types = [ //Not Tested
			0 => 'None',
			1 => 'Partial',
			2 => 'Whole',
			3 => 'Final'

		];

		return $types[$rt] ?? null;
	}
}