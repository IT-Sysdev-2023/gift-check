<?php

namespace App\Services\Finance;

use App\Helpers\ColumnHelper;
use App\Http\Resources\PromoGcRequestResource;
use App\Models\PromoGcRequest;

class ApprovedPromoGCRequestService
{
    public function approvedPromoGCRequestIndex($request)
    {
        $record = PromoGcRequest::with(['userReqby:user_id,firstname,lastname'])
            ->selectPromoApproved()
            ->whereFilter()
            ->orderByDesc('pgcreq_id')
            ->searchFilter($request)
            ->paginate()
            ->withQueryString();

        return inertia('Finance/ApprovedPromoGcRequest', [
            'data' => PromoGcRequestResource::collection($record),
            'columns' => ColumnHelper::$app_request_columns,
        ]);
    }
}
