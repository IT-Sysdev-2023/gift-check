<?php

namespace App\Services\RetailGroup;

use App\Models\PromoGcRequest;
use App\Models\PromoGcRequestItem;
use Illuminate\Support\Facades\Date;

class RetailGroupServices
{
    public function pendingGcList($request)
    {
        $data = PromoGcRequest::select(
            'pgcreq_reqby',
            'pgcreq_reqnum',
            'pgcreq_datereq',
            'pgcreq_id',
            'pgcreq_dateneeded',
            'pgcreq_total'
        )
            ->with('userReqby:user_id,firstname,lastname')
            ->where('pgcreq_group', $request->user()->usergroup)
            ->where('pgcreq_status', 'pending')
            ->where('pgcreq_group_status', '')
            ->where('pgcreq_group_status', 'approved')
            ->orWhere('pgcreq_status', 'pending')
            ->orderByDesc('pgcreq_id')
            ->get();

        $data->transform(function ($item) {
            $item->fullname = $item->userReqby->full_name;
            return $item;
        });

        return $data;
    }

    public function setupRecommendation($request)
    {
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
        )
            ->with('userReqby:user_id,firstname,lastname,usertype', 'userReqby.accessPage')
            ->where('pgcreq_group', $request->user()->usergroup)
            ->where('pgcreq_id', $request->id)
            ->where('pgcreq_status', 'pending')
            ->where('pgcreq_group_status', '')
            ->where('pgcreq_group_status', 'approved')
            ->orWhere('pgcreq_status', 'pending')
            ->first();

        if ($data) {
            $data->fullname = $data->userReqby->full_name;
            $data->time = Date::parse($data->pgcreq_datereq)->format('H:i:s');
            $data->title = $data->userReqby->accessPage->title;
        };

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
}
