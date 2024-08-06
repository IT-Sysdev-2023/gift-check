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
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class StoreGcRequestService
{

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
}
