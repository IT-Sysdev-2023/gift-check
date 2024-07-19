<?php

namespace App\Services\Finance;

use App\Helpers\ColumnHelper;
use App\Http\Resources\PromoGcRequestResource;
use App\Models\PromoGcRequest;

class PendingPromoGcRequestService
{
    public function pendingPromoGCRequestIndex($request)
    {

        $record = PromoGcRequest::with(['userReqby:user_id,firstname,lastname'])
            ->selectPromoRequest()
            ->whereFilterForPending()
            ->orderByDesc('pgcreq_id')
            ->searchFilter($request)
            ->paginate()
            ->withQueryString();

        return inertia('Finance/PendingPromoGcRequest', [
            'data' => PromoGcRequestResource::collection($record),
            'columns' => ColumnHelper::app_pend_request_columns(true),
            'promoRequestDetails' => self::getRequestDetails($request),
            'activeKey' => $request->activeKey,
        ]);
    }

    public static function getRequestDetails($request)
    {

        return PromoGcRequest::with(['userReqby.accessPage', 'approvedReq.userPrepBy'])
            ->where('pgcreq_id', $request->id)
            ->get()
            ->toArray();
    }
}
