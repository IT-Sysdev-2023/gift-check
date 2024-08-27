<?php

namespace App\Services\RetailStore;

use App\Helpers\NumberHelper;
use App\Models\ApprovedGcrequest;
use App\Models\Denomination;
use App\Models\Gc;
use App\Models\GcRelease;
use App\Models\LedgerCheck;
use App\Models\LedgerStore;
use App\Models\Store;
use App\Models\StoreGcrequest;
use App\Models\StoreReceived;
use App\Models\StoreReceivedGc;
use App\Models\TempReceivestore;
use Illuminate\Support\Facades\Date;
use App\Services\RetailStore\RetailDbServices;
use Clue\Redis\Protocol\Model\Request;
use Illuminate\Support\Facades\DB;

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
    public static function transanctionType($type)
    {
        $types = [
            '1' => 'partial',
            '2' => 'whole',
            '3' => 'final',
        ];

        return $types[$type] ?? 'none';
    }

    public function details($request)
    {
        $store = StoreReceived::where('srec_store_id', $request->user()->store_assigned)
            ->where('srec_receivingtype', 'treasury releasing')
            ->orderByDesc('srec_recid')
            ->first()->srec_recid + 1;

        // dd($store);



        $approved = ApprovedGcrequest::select(
            'agcr_request_relnum',
            'agcr_id',
            'agcr_request_id',
            'agcr_preparedby',
            'agcr_approved_at',
            'agcr_stat',
        )
            ->with('storeGcRequest:sgc_id,sgc_num,sgc_num,sgc_date_request', 'user:user_id,firstname,lastname')
            ->where('agcr_request_relnum', $request->agc_num)
            ->first();

        if ($approved) {

            $approved->dateReq = Date::parse($approved->storeGcRequest->sgc_date_request)->toFormattedDateString();
            $approved->dateApp = Date::parse($approved->agcr_approved_at)->toFormattedDateString();
            $approved->type = self::transanctionType($approved->agcr_stat);
        }


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


        $total = 0;
        $totscanned = 0;
        $totquant = 0;

        foreach ($release as $key => $value) {
            $total += $value[0]->sub;
            $totscanned += $value[0]->scanned;
            $totquant += $value[0]->quantity;
        }


        return (object) [
            'store' => $store,
            'approved' => $approved,
            'release' => $release,
            'total' => NumberHelper::currency($total),
            'totscanned' => $totscanned,
            'totquant' => $totquant,
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
            $item->dateRequest = Date::parse($item->sgc_date_request)->format('M-d-y');
            $item->dateNeeded = Date::parse($item->sgc_date_needed)->format('M-d-y');
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

    public function submitEntry($request)
    {

        $gcs = TempReceivestore::where('trec_recnum', $request->recnum)
            ->where('trec_store', $request->user()->store_assigned)
            ->where('trec_by', $request->user()->user_id)->get();

        $lastIdRecnum = StoreReceived::orderByDesc('srec_id')->first()->srec_id + 1;

        $lnumber = LedgerCheck::orderByDesc('cledger_id')->first()->cledger_no;

        $sledger_no = LedgerStore::where('sledger_store', $request->user()->store_assigned)->orderByDesc('sledger_no')->first()->sledger_no;

        $storeName = Store::where('store_id', $request->user()->store_assigned)->first()->store_name;

        $data = (object)[
            'cledger_no' => str_pad((int)$lnumber + 1, strlen($lnumber), '0', STR_PAD_LEFT),
            'sledger_no' => str_pad((int)$sledger_no + 1, strlen($sledger_no), '0', STR_PAD_LEFT),
            'storename' => $storeName,
            'recnumid' => $lastIdRecnum,
            'gcs' => $gcs,
        ];

        $transaction =  DB::transaction(function () use ($request, $data) {

            $this->dbservices
                ->storeIntoLedgerCheck($request, $data)
                ->storeIntoStoreReceived($request, $data->cledger_no)
                ->storeIntoStoreReceivedGc($request, $data)
                ->storeIntoLedgerStore($request, $data)
                ->removeTempStore($request)
                ->updateApprovedGcRequest($request);

            return true;
        });

        if ($transaction) {
            return back()->with([
                'msg' => 'Successfully Save Entry',
                'title' => 'Success',
                'status' => 'success',
            ]);
        } else {
            return back()->with([
                'msg' => 'Something Went Wrong!',
                'title' => 'Error',
                'status' => 'error',
            ]);
        }
    }
}
