<?php

namespace App\Http\Controllers\Treasury\Transactions;

use App\Http\Controllers\Controller;
use App\Http\Resources\PromoGcRequestResource;
use App\Models\PromoGcRequest;
use App\Services\Treasury\ColumnHelper;
use Illuminate\Http\Request;

class PromoGcReleasingController extends Controller
{
    public function index(Request $request)
    {
        $records = PromoGcRequest::select('pgcreq_reqby','pgcreq_reqnum', 'pgcreq_datereq', 'pgcreq_id', 'pgcreq_dateneeded', 'pgcreq_total', 'pgcreq_relstatus')
            ->with('userReqby:user_id,firstname,lastname')
            ->withWhereHas(
                'approvedReq',
                fn($q) => $q->with('user:user_id,firstname,lastname')
                    ->select('reqap_preparedby', 'reqap_trid')
                    ->where('reqap_approvedtype', 'promo gc preapproved')
            )->where([['pgcreq_status', 'approved']])
            ->orderByDesc('pgcreq_id')->paginate()->withQueryString();
        
    
            // dd(PromoGcRequestResource::collection($records)->toArray($request));
        return inertia('Treasury/Transactions/PromoGcReleasing/PromoGcReleasingIndex', [
            'title' => 'Promo Gc Releasing',
            'data' => PromoGcRequestResource::collection($records),
            'columns' => ColumnHelper::$promoGcReleasing,
            'filters' => $request->only('date', 'search')

        ]);
    }
}
