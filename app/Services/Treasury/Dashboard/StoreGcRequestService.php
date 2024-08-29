<?php

namespace App\Services\Treasury\Dashboard;

use App\Helpers\NumberHelper;
use App\Http\Resources\ApprovedGcRequestResource;
use App\Http\Resources\StoreGcRequestResource;
use App\Models\ApprovedGcrequest;
use App\Models\GcRelease;
use App\Models\InstitutPayment;
use App\Models\StoreGcrequest;
use App\Models\StoreRequestItem;
use App\Services\Documents\UploadFileHandler;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Gc;
use App\Models\GcLocation;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class StoreGcRequestService extends UploadFileHandler
{

	public function __construct()
	{
		parent::__construct();
		$this->folderName = 'approvedGCRequest';
	}
	public function pendingRequest(Request $request) //tran_release_gc.php
	{
		return StoreGcrequest::pendingRequest()->paginate()->withQueryString();
	}

	public function releasedGc(Request $request)
	{
		return ApprovedGcrequest::with([
			'user:user_id,firstname,lastname',
			'storeGcRequest:sgc_id,sgc_store,sgc_date_request',
			'storeGcRequest.store:store_id,store_name'
		])
			->select('agcr_id', 'agcr_approved_at', 'agcr_approvedby', 'agcr_preparedby', 'agcr_rec', 'agcr_request_relnum', 'agcr_request_id')
			->orderByDesc('agcr_id')
			->paginate()->withQueryString();
	}

	public function cancelledRequest(Request $request)
	{
		return StoreGcrequest::cancelledGcRequest()->paginate(10)->withQueryString();
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

		$header_data = new ApprovedGcRequestResource(ApprovedGcrequest::where('agcr_request_relnum', $id)
			->with([
				'storeGcRequest:sgc_id,sgc_store',
				'storeGcRequest.store:store_id,store_name',
				'user:user_id,firstname,lastname'
			])
			->first());

		$gcgroup = GcRelease::select('denomination.denomination', 'gc_release.re_barcode_no', 'gc_release.rel_id', 'denomination.denom_id')
			->joinGcDenomination()
			->where('rel_num', $id)
			->get();

		$paymentInfo = self::getPaymentInfo($header_data, $id);

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
			'date_released' => $header_data->agcr_approved_at->toFormattedDateString(),

			//Data/ Barcodes
			'barcode' => $gcgroup->groupBy('denomination')->map(fn($d) => $d->sortBy('denom_id')),

			//Subfooter
			'summary' => [
				'releasing_type' => self::releasingType($header_data->agcr_stat),
				'total_number_of_gc' => NumberHelper::format($total_gc->cnt),
				'total_gc_amount' => NumberHelper::format($total_gc->total),
				'payment_type' => Str::upper($header_data->agcr_paymenttype),

				'amount_receive' => $paymentInfo['amountReceive'] ?? null,
				'bank_name' => $paymentInfo['bankName'] ?? null,
				'bank_account_#' => $paymentInfo['bankAccount'] ?? null,
				'check_#' => $paymentInfo['check'] ?? null,
				'check_amount' => $paymentInfo['checkAmmount'] ?? null,
				'customer' => $paymentInfo['customer'] ?? null,
			],

			//Signatures
			'signatures' => [
				'received_by' => $header_data->agcr_recby,
				'released_by' => Str::upper($header_data->user->fullname),
				'checked_by' => Str::upper($header_data->agcr_checkedby),
			],

		];

		$pdf = Pdf::loadView('pdf.storegc', ['data' => $data]);

		$pdf->setPaper('A3');

		return $pdf->output();
	}
	public function viewCancelledGc($id)
	{
		$details = StoreGcrequest::joinCancelledGcStore()->select('sgc_id', 'sgc_num', 'sgc_requested_by', 'sgc_date_request', 'sgc_store', 'sgc_date_needed', 'sgc_remarks', 'sgc_file_docno')->where('sgc_id', $id)->first();

		$denomination = StoreRequestItem::join('denomination', 'denomination.denom_id', '=', 'store_request_items.sri_items_denomination')
			->select(DB::raw('store_request_items.sri_items_quantity * denomination.denomination as total'), 'store_request_items.sri_items_quantity', 'denomination.denomination')
			->where('store_request_items.sri_items_requestid', $id)
			->get();
		return [
			'details' => new StoreGcRequestResource($details),
			'total' => NumberHelper::currency($denomination->sum('total')),
			'denomination' => $denomination->map(function ($i) {
				$i->total = NumberHelper::currency($i->total);
				$i->denomination = NumberHelper::currency($i->denomination);
				return $i;
			}),
		];
	}

	public function scanBarcode(Request $request)
	{
		$request->validate([
			"barcode" => 'required_if:scanMode,false|nullable|digits:13',
			"bstart" => 'required_if:scanMode,true|nullable|digits:13',
			"bend" => 'required_if:scanMode,true|nullable|digits:13'
		]);

		$bstart = $request->bstart;
		$bend = $request->bend;
		$barcode = $request->barcode;
		// $relno = $request->relno;
		$denid = $request->denid;

		$reqid = $request->reqid;

		$remainGc = StoreRequestItem::where([
			['sri_items_denomination', $denid],
			['sri_items_requestid', $reqid]
		])->first('sri_items_remain');

		$sessionName = 'scanReviewGC';

		//reqid = unique request id,
		//remainGc = quantity sa denomination
		//denid = id sa kara denomination like 500 is 3 and 2000 is 5

		$responses = [];

		//If Range Mode
		if ($request->scanMode) {
			foreach (range($bstart, $bend) as $barcode) {
				$responses[] = $this->validateBarcode($request, $remainGc, $barcode, $sessionName);
			}
		} else {
			$responses[] = $this->validateBarcode($request, $remainGc, $barcode, $sessionName);
		}
		//get updated array of the session
		$sessionData = $request->session()->get($sessionName);

		return response()->json(['barcodes' => $responses, 'sessionData' => $sessionData]);
	}

	private function validateBarcode(Request $request, $remainGc, int $barcode, $sessionName)
	{
		$denid = $request->denid;
		$reqid = $request->reqid;
		$store_id = $request->store_id;

		$scannedGcSession = collect($request->session()->get($sessionName, []))->filter(function ($item) use ($reqid, $denid) {
			return ($item['reqid'] == $reqid) && ($item['denid'] == $denid);
		})->count();

		// $scannedGc = TempRelease::where([['temp_relno', $relno], ['temp_rdenom', $denid]])->count();
		if ($remainGc->sri_items_remain > $scannedGcSession) {

			// Check Barcode existence
			$whereBarcode = Gc::where('barcode_no', $barcode);

			if ($whereBarcode->exists()) {
				if ($whereBarcode->where('denom_id', $denid)->exists()) {

					$locationCheck = GcLocation::whereHas('gc', fn($q) => $q->has('denomination')->where('denom_id', $denid))
						->where([['loc_store_id', $store_id], ['loc_barcode_no', $barcode]])
						->exists();
					// Check if allocated to this store
					if ($locationCheck) {
						// Check if it is already released 
						if (GcRelease::where('re_barcode_no', $barcode)->doesntExist()) {
							// Check if gc already scanned
							$isBcExist = collect($request->session()->get($sessionName, []))->filter(function ($item) use ($reqid, $denid, $barcode) {
								return ($item['reqid'] == $reqid) && ($item['denid'] == $denid) && ($item['barcode'] == $barcode);
							});

							if ($isBcExist->isEmpty()) {

								$request->session()->push($sessionName, [
									'barcode' => $barcode,
									'denid' => $denid,
									'created_at' => now()->format('Y-d-m H:i:s'),
									'reqid' => $reqid,
									'temp_relby' => $request->user()->user_id
								]);
								return [
									'message' => "Barcode Number {$barcode} successfully scanned!",
									'status' => 200,
								];

							} else {
								return [
									'message' => "Barcode Number {$barcode} already scanned for released.",
									'status' => 400,
								];
							}
						} else {
							return [
								'message' => "Barcode Number {$barcode} already released.",
								'status' => 400,
							];
						}
					} else {
						return [
							'message' => "Barcode Number {$barcode} not found in this location.",
							'status' => 400,
						];
					}
				} else {
					return [
						'message' => "Please scan only with the same denomination.",
						'status' => 400,
					];
				}
			} else {
				return [
					'message' => "Barcode Number {$barcode} not found.",
					'status' => 400,
				];
			}

		} else {
			return [
				'message' => "Number of GC Scanned has reached the maximum number to received.",
				'status' => 400,
			];
		}

	}

	public function releasingEntrySubmit(Request $request)
	{
		// dd($request->all());
		// $request->validate([
		//     'file' => 'required',
		//     'remarks' => 'required',
		//     "receivedBy" => 'required',
		//     'paymentType.type' => 'required',
		//     'paymentType.amount' => 'required_if:paymentType.type,cash',
		//     'paymentType.customer' => 'required_if:paymentType.type,jv',
		//     "checkedBy" => 'required',
		//     "rid" => 'required'
		// ], [
		//     'paymentType.customer' => 'The customer field is required when payment type is jv.',
		//     'paymentType.amount' => 'The amount field is required when payment type is cash.',
		// ]);

		$latestRecord = ApprovedGcrequest::max('agcr_request_relnum');
		$relid = $latestRecord ? $latestRecord + 1 : 1;

		$scannedBc = collect($request->session()->get('scanReviewGC', []))->filter(function ($item) use ($request) {
			return $item['reqid'] === $request->rid;
		});

		$rgc = StoreRequestItem::select('sri_items_remain as qty', 'sri_items_denomination as denom_id')
			->where('sri_items_requestid', $request->rid)
			->whereNot('sri_items_remain', 0)
			->get();

		//check if the denominations gc already scanned
		$isScanned = $rgc->map(function ($sr) use ($scannedBc) {
			$s = $scannedBc->where('denid', $sr->denom_id)->count();
			return $sr->qty === $s;
		});

		// dd($scannedBc);
		if ($isScanned->every(fn($n) => $n)) {

			DB::transaction(function () use ($scannedBc, $request, $relid) {

				$scannedBc->each(function ($i) use ($request, $relid) {

					$reqId = $request->rid;
					GcRelease::create([
						're_barcode_no' => $i['barcode'],
						'rel_storegcreq_id' => $reqId,
						'rel_store_id' => $request->store_id,
						'rel_num' => $relid,
						'rel_date' => $i['created_at'],
						'rel_by' => $request->user()->user_id
					]);

					$remain = StoreRequestItem::where([['sri_items_requestid', $reqId], ['sri_items_denomination', $i['denid']]])->first('sri_items_remain')->sri_items_remain;
					$remain--;

					StoreRequestItem::where([['sri_items_denomination', $i['denid']], ['sri_items_requestid', $reqId]])
						->update([
							'sri_items_remain' => $remain
						]);

					GcLocation::where('loc_barcode_no', $i['barcode'])
						->update(['loc_rel' => '*']);
				});

				$check = StoreRequestItem::where('sri_items_requestid', $request->rid)->whereNot('sri_items_remain', '0');



				// if(checkIfPartialWhole($link,$reqId))
				// 			function checkIfPartialWhole($link,$reqId)
				// {
				// 	$status = true;
				// 	$query = $link->query(
				// 		"SELECT 
				// 			`sri_items_remain` 
				// 		FROM 
				// 			`store_request_items` 
				// 		WHERE 
				// 			`sri_items_remain`!='0'
				// 		AND
				// 			`sri_items_requestid`='$reqId'
				// 	");

				// 	if($query)
				// 	{
				// 		while ($row = $query->fetch_object()) {
				// 			if($row->sri_items_remain>0)
				// 			{
				// 				$status = false;
				// 				break;
				// 			}
				// 		}
				// 		if($status)
				// 		{
				// 			return true;
				// 		}
				// 		else 
				// 		{
				// 			return false;
				// 		}
				// 	}
				// 	else 
				// 	{
				// 		echo $link->error;
				// 	}
				// }
			});
















		} else {
			return response()->json('Please scan the Barcode First', 400);
		}


	}
	private static function getPaymentInfo($header_data, $id)
	{
		$paymentInfo = [];

		if (!empty($header_data->agcr_paymenttype)) {

			$payment = InstitutPayment::select('insp_trid', 'insp_paymentcustomer', 'institut_bankname', 'institut_bankaccountnum', 'institut_checknumber', 'institut_amountrec', 'institut_jvcustomer')
				->where([['insp_trid', $id + 2], ['insp_paymentcustomer', 'stores']])
				->first();

			$paymentInfo = match ($header_data->agcr_paymenttype) {
				'cash' => [
					'amountReceive' => NumberHelper::format($payment->institut_amountrec)
				],
				'check' => [
					'bankName' => Str::upper($payment->institut_bankname),
					'bankAccount' => Str::upper($payment->institut_bankaccountnum),
					'check' => Str::upper($payment->institut_checknumber),
					'checkAmmount' => NumberHelper::format($payment->institut_amountrec)
				],
				'jv' => [
					'customer' => Str::upper($payment->institut_jvcustomer)
				],
				default => []
			};
		}

		return $paymentInfo;
	}
	private static function releasingType(int $rt)
	{
		$types = [ //Not Tested
			0 => 'None',
			1 => 'Partial',
			2 => 'Whole',
			3 => 'Final'
		];
		return $types[$rt] ?? null;
	}

	// public function scanRangeBarcode(Request $request)
	// {
	//     $request->validate([
	//         'bstart' => 'required',
	//         'bend' => 'required',
	//         'relid' => 'required',
	//         'store_id' => 'required',
	//         'reqid' => 'required',
	//     ]);
	//     $bend = $request->bend;
	//     $bstart = $request->bstart;

	//     $gcTotal = $bend - $bstart + 1;

	//     dd($gcTotal);

	//     $denid = Gc::where('barcode_no', $bstart)->first('denom_id')->denom_id;

	//     $remainGc = StoreRequestItem::where([['sri_items_denomination', $denid], ['sri_items_requestid', $request->reqid]])
	//         ->first('sri_items_remain')->sri_items_remain;

	//     $scannedGc = TempRelease::where([['temp_relno', $request->relid], ['temp_rdenom', $denid]])
	//         ->count();

	//     $gctotal = $gcTotal + $scannedGc;

	//     $nums = 0;

	//     if ($gctotal > $remainGc) {
	//         return response()->json('Number of GC Scanned has reached the maximum number to received.', 400);
	//     } else {
	//         foreach (range($bstart, $bend) as $bc) {
	//             if (Gc::where('barcode_no', $bc)->doesntExist()) {
	//                 return response()->json("Barcode Number {$bc} not found.", 400);
	//             }

	//             $locationCheck = GcLocation::whereHas('gc', fn($q) => $q->has('denomination')->where('denom_id', $denid))
	//                 ->where([['loc_store_id', $request->store_id], ['loc_barcode_no', $bc]])
	//                 ->doesntExist();

	//             if ($locationCheck) {
	//                 return response()->json("Barcode Number {$bc} not found in this location.", 400);
	//             }

	//             if (GcRelease::where('re_barcode_no', $bc)->exists()) {
	//                 return response()->json("Barcode Number {$bc} already released.", 400);
	//             }

	//             if (TempRelease::where('temp_rbarcode', $bc)->exists()) {
	//                 return response()->json("Barcode Number {$bc} already scanned for released. ", 400);
	//             } else {
	//                 TempRelease::create([
	//                     'temp_rbarcode' => $bc,
	//                     'temp_rdenom' => $denid,
	//                     'temp_rdate' => now(),
	//                     'temp_relno' => $request->relid,
	//                     'temp_relby' => $request->user()->user_id
	//                 ]);

	//             }


	//         }

	//         return response()->json("GC Barcode #{$bstart} to {$bend} successfully validated.");
	//     }


	// }
}
