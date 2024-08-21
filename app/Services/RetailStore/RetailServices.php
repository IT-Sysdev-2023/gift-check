<?php

namespace App\Services\RetailStore;

use App\Helpers\NumberHelper;
use App\Models\ApprovedGcrequest;
use App\Models\GcRelease;
use App\Models\StoreReceived;
use Illuminate\Support\Facades\Date;

class RetailServices
{
    public function getDataApproved()
    {
        $data = ApprovedGcrequest::select(
            'agcr_request_relnum',
            'agcr_approved_at',
            'agcr_request_id',
            'agcr_approvedby',
            'agcr_preparedby',
            'agcr_rec',
            'agcr_id',
        )
            ->with(
                'storeGcRequest:sgc_id,sgc_store,sgc_date_request',
                'storeGcRequest.store:store_id,store_name',
                'user:user_id,firstname,lastname'
            )
            ->whereHas('storeGcRequest', function ($query) {
                $query->where('sgc_store', request()->user()->store_assigned);
            })
            ->orderByDesc('agcr_request_relnum')
            ->paginate(10)->withQueryString();

        $data->transform(function ($item) {
            $item->spgc_date_request = Date::parse($item->storeGcRequest->sgc_date_request)->toFormattedDateString();
            $item->agcr_date = $item->agcr_approved_at->toFormattedDateString();
            $item->storename = $item->storeGcRequest->store->store_name;
            $item->fullname = $item->user->full_name;

            return $item;
        });

        return $data;
    }

    public function details($request)
    {
        $store = StoreReceived::where('srec_store_id', $request->user()->store_assigned)
            ->where('srec_receivingtype', 'treasury releasing')
            ->orderByDesc('srec_recid')
            ->get();

        $approved =  ApprovedGcrequest::select('agcr_request_relnum', 'agcr_id', 'agcr_request_id', 'agcr_preparedby', 'agcr_approved_at')
            ->with('storeGcRequest:sgc_id,sgc_num,sgc_num', 'user:user_id,firstname,lastname')
            ->where('agcr_id', $request->agc_num)
            ->get();

        $release = GcRelease::with('gc:denom_id,barcode_no', 'gc.denomination:denom_id,denomination')
            ->where('rel_num', $request->agc_num)
            ->get();

        $release->transform(function ($item) use ($request) {

            $item->denom = $item->gc->denomination->denomination;

            $item->quantity = self::getQuantity($request, $item);

            $item->sub = $item->quantity * $item->denom;

            return $item;

        });

        $total = NumberHelper::currency($release->sum('sub'));

        return (object) [
            'store' => $store,
            'approved' => $approved,
            'release' => $release,
            'total' => $total,
        ];
    }
    public static function getQuantity($request, $item)
    {

        return GcRelease::whereHas('gc', function ($query) use ($item) {
            $query->where('denom_id', $item->gc->denom_id);
        })->where('rel_num', $request->agc_num)->count();
    }
}
