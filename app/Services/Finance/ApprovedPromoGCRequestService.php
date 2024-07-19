<?php

namespace App\Services\Finance;

use App\Helpers\ColumnHelper;
use App\Http\Resources\PromoGcRequestResource;
use App\Models\PromoGcRequest;

class ApprovedPromoGCRequestService
{
    public function approvedPromoGCRequestIndex()
    {
        $record = PromoGcRequest::with(['userReqby:user_id,firstname,lastname'])
            ->selectPromoApproved()
            ->where('pgcreq_group', '!=', '')
            ->where('pgcreq_group_status', 'approved')
            ->where('pgcreq_status', 'approved')
            ->orderByDesc('pgcreq_id')
            ->paginate()
            ->withQueryString();

        return inertia('Finance/ApprovedPromoGcRequest', [
            'data' => PromoGcRequestResource::collection($record),
            'columns' => ColumnHelper::$app_request_columns,
        ]);
    }
}
