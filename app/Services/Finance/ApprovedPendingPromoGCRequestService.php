<?php

namespace App\Services\Finance;

use App\Helpers\ColumnHelper;
use App\Helpers\NumberHelper;
use App\Http\Resources\PromoGcDetailResource;
use App\Http\Resources\PromoGcRequestResource;
use App\Models\PromoGcRequest;
use App\Models\PromoGcRequestItem;

class ApprovedPendingPromoGCRequestService
{
    public function pendingPromoGCRequestIndex($request)
    {
        return inertia('Finance/PendingPromoGcRequest', [
            'data' => PromoGcRequestResource::collection(
                PromoGcRequest::with(['userReqby:user_id,firstname,lastname'])
                    ->selectPromoRequest()
                    ->whereFilterForPending()
                    ->orderByDesc('pgcreq_id')
                    ->searchFilter($request)
                    ->paginate()
                    ->withQueryString()
            ),
            'columns' => ColumnHelper::app_pend_request_columns(true),
            'details' => PromoGcDetailResource::collection(self::getRequestDetails($request)),
            'activeKey' => $request->activeKey,
            'denomination' => self::getDenomination($request->id),
        ]);
    }

    public static function getRequestDetails($request)
    {

        return  PromoGcRequest::selectPendingRequest()->with([
            'userReqby' => function ($query) {
                $query->select('usertype', 'user_id', 'firstname', 'lastname');
            },
            'userReqby.accessPage' => function ($query) {
                $query->select('access_no', 'title');
            }
        ])
            ->where('pgcreq_id', $request->id)
            ->get();
    }

    public function approvedPromoGCRequestIndex($request)
    {
        return inertia('Finance/ApprovedPromoGcRequest', [
            'data' => PromoGcRequestResource::collection(
                PromoGcRequest::with(['userReqby:user_id,firstname,lastname'])
                    ->selectPromoRequest()
                    ->whereFilterForApproved()
                    ->orderByDesc('pgcreq_id')
                    ->searchFilter($request)
                    ->paginate()
                    ->withQueryString()
            ),
            'columns' => ColumnHelper::app_pend_request_columns(false),
        ]);
    }

    public static function getDenomination($id)
    {
        $data =  PromoGcRequestItem::select('pgcreqi_qty', 'pgcreqi_denom')
            ->where('pgcreqi_trid', $id)
            ->with('denomination:denom_id,denomination')
            ->get();

        $data->transform(function ($item){
            $item->subt = $item->denomination->denomination * $item->pgcreqi_qty;
            $item->subtotal = NumberHelper::currency($item->denomination->denomination * $item->pgcreqi_qty);
            $item->denomination->denomination = NumberHelper::currency($item->denomination->denomination);
            return $item;
        });
        return (object)[
            'data' => $data,
            'total' => NumberHelper::currency($data->sum('subt')),
        ];
    }
}
