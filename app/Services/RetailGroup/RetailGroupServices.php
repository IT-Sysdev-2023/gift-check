<?php

namespace App\Services\RetailGroup;

use App\Models\ApprovedRequest;
use App\Models\CancelledRequest;
use App\Models\PromoGcRequest;
use App\Models\PromoGcRequestItem;
use App\Models\PromoLedger;
use App\Services\Documents\FileHandler;
use Carbon\Carbon;
use Illuminate\Support\Facades\Date;

class RetailGroupServices extends FileHandler
{
    public function __construct()
    {
        parent::__construct();

        $this->folderName = "approveddocs";
    }
    public function pendingGcList($request)
    {
        $data = PromoGcRequest::select(
            'pgcreq_reqby',
            'pgcreq_reqnum',
            'pgcreq_datereq',
            'pgcreq_id',
            'pgcreq_dateneeded',
            'pgcreq_total',
            'pgcreq_group_status'
        )
            ->with('userReqby:user_id,firstname,lastname')
            ->where('pgcreq_group', $request->user()->usergroup)
            ->where('pgcreq_status', 'pending')
            ->where(function ($q) {
                $q->where('pgcreq_group_status', '')
                    ->orWhere('pgcreq_group_status', 'approved');
            })
            ->orderByDesc('pgcreq_id')
            ->paginate(10)->withQueryString();

        $data->transform(function ($item) {
            $item->fullname = $item->userReqby->full_name;
            $item->needed = Date::parse($item->pgcreq_dateneeded)->toFormattedDateString();
            $item->req = Date::parse($item->pgcreq_datereq)->toFormattedDateString();
            return $item;
        });


        return $data;
    }

    public function setupRecommendation($request)
    {
        // dd($request->user()->usergroup);
        $data = PromoGcRequest::select(
            'pgcreq_reqby',
            'pgcreq_reqnum',
            'pgcreq_datereq',
            'pgcreq_id',
            'pgcreq_dateneeded',
            'pgcreq_total',
            'pgcreq_group',
            'pgcreq_doc',
            'pgcreq_total',
            'pgcreq_remarks',
            'pgcreq_status',
            'pgcreq_group_status',
        )
            ->with('userReqby:user_id,firstname,lastname,usertype', 'userReqby.accessPage')
            ->where('pgcreq_id', $request->id)
            ->where('pgcreq_group', $request->user()->usergroup)
            ->where('pgcreq_status', 'pending')
            ->where(function ($query) {
                $query->where('pgcreq_group_status', '')
                    ->orWhere('pgcreq_group_status', 'approved');
            })
            ->first();

        if ($data) {
            $data->fullname = $data->userReqby->full_name;
            $data->time = Date::parse($data->pgcreq_datereq)->format('H:i:s');
            $data->title = $data->userReqby->accessPage->title;
        };

        return $data;
    }

    public function approvedData($request)
    {

        $data = ApprovedRequest::select('reqap_trid', 'reqap_preparedby', 'reqap_date', 'reqap_doc')
            ->with('user:user_id,firstname,lastname')
            ->where('reqap_trid', $request->id)
            ->where('reqap_approvedtype', 'promo gc preapproved')
            ->first();

        if ($data) {
            // dd(Date::parse($data->reqap_date));
            $data->time = Date::parse($data->reqap_date)->format('H:i:s');
            $data->fullname = $data->user->full_name;
        }
        return $data;
    }
    public function denomination($request)
    {
        $data = PromoGcRequestItem::with('denomination:denom_id,denomination')
            ->where('pgcreqi_trid', $request->id)
            ->get();

        $data->transform(function ($item) {
            $item->denom = $item->denomination->denomination;
            $item->subtotal = $item->pgcreqi_qty * $item->denom;
            $item->orderBy('denom');
            return $item;
        });

        $total = $data->sum('subtotal');

        return (object) [
            'data' => $data,
            'total' => $total,
        ];
    }

    public function updatePromoGcRequest($request)
    {
        if ($request->status == '1') {
            //Approved Request
            PromoGcRequest::where('pgcreq_status', 'pending')
                ->where('pgcreq_group_status', '')
                ->where('pgcreq_id', $request->id)
                ->update(['pgcreq_group_status' => 'approved']);
        } else {
            //Canecelled Request
            PromoGcRequest::where('pgcreq_status', 'pending')
                ->where('pgcreq_group_status', '')
                ->where('pgcreq_id', $request->id)
                ->update(['pgcreq_group_status' => 'cancelled']);
        }
        return $this;
    }

    public function insertIntoApprovedRequest($request)
    {
        // dd(now());
        $file = $this->createFileName($request);

        if ($request->status == '1') {

            $wasCreated = ApprovedRequest::create([
                'reqap_trid' => $request->id,
                'reqap_approvedtype' => 'promo gc preapproved',
                'reqap_remarks' => $request->remarks,
                'reqap_doc' => $file ?? '',
                'reqap_preparedby' => $request->user()->user_id,
                'reqap_date' => Carbon::now(),
            ]);

            if ($wasCreated->wasRecentlyCreated) {
                $this->saveFile($request, $file);
            }
        } else {

            $wasCancelled = CancelledRequest::create([
                'reqcan_trid' => $request->id,
                'reqcan_canceltype' => 'promo gc preapproved',
                'reqcan_remarks' => $request->remarks,
                'reqcan_doc' => $request->docs ?? '',
                'reqcan_preparedby' =>  $request->user()->user_id,
                'reqcan_date' => Carbon::now(),
            ]);

            if ($wasCancelled->wasRecentlyCreated) {
                $this->saveFile($request, $file);
            }
        }

        return $this;
    }
    public function insertIntoPromoLedger($request)
    {
        PromoLedger::create([
            'promled_desc' => 'promo request approval',
            'promled_debit' => $request->total,
            'promled_trid' => $request->id,
        ]);

        return $this;
    }

    // public function sample()
    // {
    //     $data = PromoGcRequest::with('promoitems', 'promoitems.denomination')->where('pgcreq_id', $request->id)->first();

    //     $data->promoitems->transform(function ($item) {

    //         $item->subtotal = $item->pgcreqi_qty * $item->denomination->denomination;

    //         return $item;
    //     });

    //     $total = $data->promoitems->sum('subtotal');

    //     dd($total);

    // }
}
