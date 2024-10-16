<?php

namespace App\Services\RetailGroup;

use App\Helpers\NumberHelper;
use App\Models\ApprovedRequest;
use App\Models\CancelledRequest;
use App\Models\PromoGcRequest;
use App\Models\PromoGcRequestItem;
use App\Models\PromoLedger;
use App\Services\Documents\FileHandler;
use Carbon\Carbon;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Str;

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
                ->update(
                    [
                        'pgcreq_group_status' => 'cancelled',
                        'pgcreq_updateby' => $request->user()->user_id,
                        'cancellremarks' => $request->remarks,
                    ]
                );
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
                'reqcan_preparedby' => $request->user()->user_id,
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

    public function getPromoApprovedRequest()
    {
        $data = PromoGcRequest::select(
            'pgcreq_total',
            'pgcreq_id',
            'pgcreq_reqnum',
            'pgcreq_reqby',
            'pgcreq_datereq',
            'pgcreq_dateneeded',
            'pgcreq_group'
        )->where('pgcreq_status', 'approved')
            ->with('userReqby:user_id,firstname,lastname')
            ->withWhereHas('approvedReq', function ($q) {
                $q->select('reqap_approvedtype', 'reqap_preparedby', 'reqap_trid', 'reqap_id', 'reqap_date')
                    ->with('user:user_id,firstname,lastname')
                    ->where('reqap_approvedtype', 'promo gc preapproved');
            })->orderByDesc('pgcreq_reqnum')->paginate(10)->withQueryString();


        $data->transform(function ($item) {

            $app = ApprovedRequest::select('firstname', 'lastname', 'reqap_preparedby')
                ->join('users', 'user_id', '=', 'reqap_preparedby')
                ->where('reqap_trid', $item->pgcreq_id)
                ->where('reqap_approvedtype', 'promo gc approved')->first();

            $item->appby = Str::ucfirst($app->firstname) . ', ' . Str::ucfirst($app->lastname);
            $item->reqby = $item->userReqby->full_name;
            $item->recby = $item->approvedReq->user->full_name;
            $item->reqdate = $item->pgcreq_datereq->toFormattedDateString();
            $item->dateneed = $item->pgcreq_dateneeded->toFormattedDateString();

            return $item;
        });

        return $data;
    }

    public function getPromoApprovedRequestDetails($id)
    {
        $data = PromoGcRequest::where('pgcreq_id', $id)->where('pgcreq_status', 'approved')
            ->with('userReqby:user_id,firstname,lastname')
            ->withWhereHas('approvedReq', function ($q) {
                $q->with('user:user_id,firstname,lastname')
                    ->where('reqap_approvedtype', 'promo gc preapproved');
            })->first();

        if ($data) {
            $data->reqdate = $data->approvedReq->reqap_date->toFormattedDateString();
            $data->reqtime = $data->approvedReq->reqap_date->format('h:s A');
        };

        $denomination = PromoGcRequestItem::select('pgcreqi_qty', 'denomination')
            ->join('denomination', 'denom_id', '=', 'pgcreqi_denom')
            ->where('pgcreqi_trid', $id)
            ->get();

        $denomination->transform(function ($item) {
            $item->sub = $item->pgcreqi_qty * $item->denomination;
            return $item;
        });

        $approved = ApprovedRequest::select(
            'reqap_remarks',
            'reqap_doc',
            'reqap_approvedby',
            'reqap_checkedby',
            'reqap_date',
            'reqap_preparedby'
        )->with('user:user_id,firstname,lastname')
            ->where('reqap_trid', $id)
            ->where('reqap_approvedtype', 'promo gc approved')
            ->first();

            if($approved){
                $approved->reqdate = $approved->reqap_date->toFormattedDateString();
                $approved->reqtime = $approved->reqap_date->format('h:s A');
            }

        return (object) [
            'data' => $data,
            'denom' => [
                'denomdata' => $denomination,
                'total' => NumberHelper::currency($denomination->sum('sub')),
            ],
            'approved' => $approved,
        ];
    }
}
