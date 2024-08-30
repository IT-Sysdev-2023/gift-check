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

        //     $table = 'promo_gc_request';
        // $select = "promo_gc_request.pgcreq_reqnum,
        //     promo_gc_request.pgcreq_datereq,
        //     promo_gc_request.pgcreq_id,
        //     promo_gc_request.pgcreq_dateneeded,
        //     promo_gc_request.pgcreq_total,
        //     promo_gc_request.pgcreq_relstatus,
        //     CONCAT(users.firstname,' ',users.lastname) as prep,
        //     CONCAT(recom.firstname,' ',recom.lastname) as recby,
        //     promo_gc_request.pgcreq_relstatus";
        // $where = "promo_gc_request.pgcreq_status='approved'
        //     AND
        //     approved_request.reqap_approvedtype='promo gc preapproved'";
        // $join = 'INNER JOIN
        //         users
        //     ON
        //         users.user_id = promo_gc_request.pgcreq_reqby
        //     LEFT JOIN
        //         approved_request
        //     ON
        //         approved_request.reqap_trid = promo_gc_request.pgcreq_id
        //     LEFT JOIN
        //         users as recom
        //     ON
        //         recom.user_id = approved_request.reqap_preparedby';
        // $limit = 'ORDER BY pgcreq_id DESC';

        // $request = getAllData($link,$table,$select,$where,$join,$limit);

        $records = PromoGcRequest::select('pgcreq_reqby','pgcreq_reqnum', 'pgcreq_datereq', 'pgcreq_id', 'pgcreq_dateneeded', 'pgcreq_total', 'pgcreq_relstatus')
            ->with('userReqby:user_id,firstname,lastname')->withWhereHas(
                'approvedReq',
                fn($q) => $q->with('user:user_id,firstname,lastname')
                    ->select('reqap_preparedby', 'reqap_trid')
                    ->where('reqap_approvedtype', 'promo gc preapproved')
            )->where([['pgcreq_status', 'approved']])
            ->orderByDesc('pgcreq_id')->get();

        dd(PromoGcRequestResource::collection($records)->toArray($request));
        return inertia('Treasury/Transactions/PromoGcReleasing/PromoGcReleasingIndex', [
            'title' => 'Promo Gc Releasing',
            'data' => [],
            'columns' => ColumnHelper::$promoGcReleasing,
            'filters' => $request->only('date', 'search')

        ]);
    }
}
