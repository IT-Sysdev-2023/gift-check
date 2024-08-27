<?php

namespace App\Services\RetailStore;

use App\Helpers\NumberHelper;
use App\Models\ApprovedGcrequest;
use App\Models\Gc;
use App\Models\GcRelease;
use App\Models\StoreGcrequest;
use App\Models\StoreReceived;
use App\Models\StoreReceivedGc;
use App\Models\TempReceivestore;
use Illuminate\Support\Facades\Date;
use App\Services\RetailStore\RetailDbServices;
use Clue\Redis\Protocol\Model\Request;

class RetailServices
{
    public function __construct(public RetailDbServices $dbservices) {}
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
        // dd($request->agc_num);
        $store = StoreReceived::where('srec_store_id', $request->user()->store_assigned)
            ->where('srec_receivingtype', 'treasury releasing')
            ->orderByDesc('srec_recid')
            ->first()->srec_recid + 1;



        $approved = ApprovedGcrequest::select('agcr_request_relnum', 'agcr_id', 'agcr_request_id', 'agcr_preparedby', 'agcr_approved_at')
            ->with('storeGcRequest:sgc_id,sgc_num,sgc_num', 'user:user_id,firstname,lastname')
            ->where('agcr_id', $request->agc_num)
            ->get();

        $release = GcRelease::with(['gc:denom_id,barcode_no', 'gc.denomination:denom_id,denomination', 'store:store_id,store_name'])
            ->where('rel_num', $request->agc_num)
            ->get()
            ->groupBy('gc.denom_id');

        $release->map(function ($group) use ($request) {

            return $group->map(function ($item) use ($request) {

                $count = TempReceivestore::where('trec_denid', $item->gc->denom_id)->where('trec_by', $request->user()->user_id)->count();

                $item->scanned = $count;

                $item->denom = $item->gc->denomination->denomination;

                $item->quantity = self::getQuantity($request, $item);

                $item->sub = $item->quantity * $item->denom;

                return $item;
            });
        });

        // dd($release->toArray());


        $total = 0;

        foreach ($release as $key => $value) {
            $total += $value[0]->sub;
        }


        return (object) [
            'store' => $store,
            'approved' => $approved,
            'release' => $release,
            'total' => NumberHelper::currency($total),
        ];
    }

    public static function getQuantity($request, $item)
    {

        return GcRelease::whereHas('gc', function ($query) use ($item) {
            $query->where('denom_id', $item->gc->denom_id);
        })->where('rel_num', $request->agc_num)->count();
    }

    public function countGcPendingRequest()
    {
        $storeId = request()->user()->store_assigned;
        $results = StoreGcrequest::join('stores', 'store_gcrequest.sgc_store', '=', 'stores.store_id')
            ->join('users', 'users.user_id', '=', 'store_gcrequest.sgc_requested_by')
            ->where(function ($query) {
                $query->where('store_gcrequest.sgc_status', 0)
                    ->orWhere('store_gcrequest.sgc_status', 1);
            })
            ->where('store_gcrequest.sgc_store', $storeId)
            ->where('store_gcrequest.sgc_cancel', '')
            ->get();
        $results->transform(function ($item) {
            $item->dateRequest = Date::parse($item->sgc_date_request)->format('F-d-y');
            $item->dateNeeded = Date::parse($item->sgc_date_needed)->format('F-d-y');
            $item->requestedBy = $item->firstname . ' ' . $item->lastname;

            return $item;
        });

        return $results;
    }

    public function validateBarcode($request)
    {

        $existInGc = Gc::where('barcode_no', $request->barcode);

        $released = GcRelease::where('re_barcode_no', $request->barcode)
            ->where('rel_store_id', $request->user()->store_assigned)
            ->exists();

        $scanned = TempReceivestore::where('trec_barcode', $request->barcode)->where('trec_recnum', $request->recnum)
            ->exists();

        $received = StoreReceivedGc::where('strec_barcode', $request->barcode)->exists();

        if ($existInGc->exists()) {

            if ($existInGc->first()->denom_id == $request->denom_id) {

                if ($released) {
                    if (!$scanned) {
                        if (!$received) {

                            $this->dbservices->temReceivedStoreCreation($request);

                        } else {
                            return back()->with([
                                'msg' => 'Barcode Already Received',
                                'title' => 'Received',
                                'status' => 'warning',
                            ]);
                        }
                    } else {
                        return back()->with([
                            'msg' => 'Barcode Already Scanned',
                            'title' => 'Scanned',
                            'status' => 'warning',
                        ]);
                    }
                } else {

                    return back()->with([
                        'msg' => 'Opps Barcode is Invalid in this Location',
                        'title' => 'Wrong Denomination',
                        'status' => 'error',
                    ]);
                }
            } else {
                return back()->with([
                    'msg' => 'Opps Its Looks like Denomination mismatch',
                    'title' => 'Wrong Denomination',
                    'status' => 'error',
                ]);
            }
        } else {

            return back()->with([
                'msg' => 'Barcode Dont Exists',
                'title' => 'Error Not Found',
                'status' => 'error',
            ]);
        }
    }
}
