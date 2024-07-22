<?php

namespace App\Services\Finance;

use App\Helpers\ColumnHelper;
use App\Http\Resources\PromoGcRequestResource;
use App\Models\PromoGcRequest;

class ApprovedPendingPromoGCRequestService
{
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
            'details' => self::getRequestDetails($request),
            'activeKey' => $request->activeKey,
        ]);
    }

    public static function getRequestDetails($request)
    {

        return PromoGcRequest::selectPendingRequest()->with([
            'userReqby' => function ($query) {
                $query->select('usertype', 'user_id', 'firstname', 'lastname');
            },
            'userReqby.accessPage' => function ($query) {
                $query->select('access_no', 'title');
            }
        ])
            ->where('pgcreq_id', $request->id)
            ->get()->toArray();
    }
}
