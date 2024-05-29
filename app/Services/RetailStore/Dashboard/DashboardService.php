<?php

namespace App\Services\RetailStore\Dashboard;

use App\Http\Resources\soldGcResource;
use App\Models\ApprovedGcrequest;
use App\Models\Denomination;
use App\Models\StoreGcrequest;
use App\Models\StoreReceivedGc;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardService
{

	private $denomination;
	public $request;
	public function __construct(Request $request)
	{
		$this->denomination = Denomination::denomation();

		$this->request = $request;
	}

	private function storeAssigned()
	{
		return $this->request->user()->store_assigned;
	}


	public function pendingGcRequest()
	{
		return StoreGcrequest::pendingRequest()->count();
	}

	public function releasedGc()
	{
		$storeid = $this->storeAssigned();

		return ApprovedGcrequest::with('storeGcRequest')
			->where('sgc_store', $storeid)
			->count();
	}

	public function cancelledGcRequest()
	{
		$storeid = $this->storeAssigned();

		return StoreGcrequest::where([['sgc_cancel', '*'], ['sgc_store', $storeid], ['sgc_status', '0']])
			->count();
	}

	public function availableGc()
	{
		$storeid = $this->storeAssigned();

		$record = collect();
		$this->denomination->each(function (mixed $item, int $key) use ($storeid, &$record) {

			$storeRecieved = StoreReceivedGc::where([
				['strec_storeid', $storeid],
				['strec_denom', $item->denom_id],
				['strec_sold', ''],
				['strec_transfer_out', ''],
				['strec_bng_tag', ''],
			])->count();

			$record[] = [
				'availableGc' => $storeRecieved,
				'denomination' => $item->denomination
			];
		});

		return $record;
	}

	public function soldGc()
	{
		$storeid = $this->storeAssigned();

		$record = collect();
		$this->denomination->each(function ($item, $key) use ($storeid, &$record) {
			$storeRecieved = StoreReceivedGc::where([
				['strec_storeid', $storeid],
				['strec_denom', $item->denom_id],
				['strec_sold', '*']
			])->count();

			$record[] = [
				'availableGc' => $storeRecieved,
				'denomination' => $item->denomination
			];
		});

		return $record;
	}

	public function viewAvailableGc(Request $request)
    {
        $storeId = $request->user()->store_assigned;

        $record = StoreReceivedGc::join('gc_release', 'store_received_gc.strec_barcode', '=', 'gc_release.re_barcode_no')
            ->join('store_gcrequest', 'gc_release.rel_storegcreq_id', '=', 'store_gcrequest.sgc_id')
            ->join('denomination', 'store_received_gc.strec_denom', '=', 'denomination.denom_id')
            ->leftJoin('transaction_refund', 'store_received_gc.strec_barcode', '=', 'transaction_refund.refund_barcode')
            ->leftJoin('transaction_stores', 'transaction_refund.refund_trans_id', '=', 'transaction_stores.trans_sid')
            ->select(
                'strec_barcode',
                'strec_return',
                'gc_release.rel_storegcreq_id',
                'denomination.denomination',
                'transaction_refund.refund_trans_id',
                'transaction_stores.trans_datetime',
                'store_gcrequest.sgc_num'
            )
            ->where([
                ['strec_sold', ''],
                ['strec_storeid', $storeId],
                ['strec_transfer_out', ''],
                ['strec_bng_tag', '']
            ])
            ->orderByDesc('transaction_refund.refund_id')
            ->get()
            ->groupBy('strec_barcode');

        return $record;
        // return response()->json(['data' => $record]);

    }
	public function viewSoldGc()
    {
        $record = StoreReceivedGc::join('denomination', 'strec_denom', '=', 'denom_id')
            ->join('transaction_sales', 'strec_barcode', '=', 'sales_barcode')
            ->join('transaction_stores', 'sales_transaction_id', '=', 'trans_sid')
            ->leftJoin('store_verification', 'strec_barcode', '=', 'vs_barcode')
            ->leftJoin('stores', 'vs_store', '=', 'store_id')
            ->select(
                'vs_barcode',
                'strec_barcode',
                'denomination',
                'vs_date',
                'strec_recnum',
                'trans_number',
                'trans_type',
                'trans_datetime',
                'store_name'
            )
            ->where([
                ['strec_sold', '*'],
                ['strec_return', ''],
                ['strec_storeid', request()->user()->store_assigned],
                ['sales_item_status', '0']
            ])
            ->orderByDesc('trans_datetime')
            ->paginate()
            ->withQueryString();
            // ->groupBy('strec_barcode');

        return ($record);
    }

}