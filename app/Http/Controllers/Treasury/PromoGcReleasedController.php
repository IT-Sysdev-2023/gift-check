<?php

namespace App\Http\Controllers\Treasury;

use App\Helpers\NumberHelper;
use App\Http\Controllers\Controller;
use App\Models\PromoGcReleaseToDetail;
use App\Models\PromoGcReleaseToItem;
use App\Services\Treasury\ColumnHelper;
use Illuminate\Http\Request;

class PromoGcReleasedController extends Controller
{
    public function released(Request $request)
    {

        $record = PromoGcReleaseToDetail::with('user:user_id,firstname,lastname', 'promoGcRequest:pgcreq_id,pgcreq_reqnum')
            ->select('prrelto_trid', 'prrelto_relby', 'prrelto_id', 'prrelto_relnumber', 'prrelto_docs', 'prrelto_checkedby', 'prrelto_approvedby', 'prrelto_date', 'prrelto_recby', 'prrelto_status')
            ->orderByDesc('prrelto_id')
            ->paginate()
            ->withQueryString();

        $record->transform(function ($item) {
            $item->prrelto_relnumber = NumberHelper::leadingZero($item->prrelto_relnumber, '%03d');
            $item->date = $item->prrelto_date->toFormattedDateString();
            return $item;
        });
        return inertia('Treasury/Dashboard/PromoGcReleasing/PromoGcReleasing', [
            'title' => 'Promo Gc Released',
            'data' => $record,
            'columns' => ColumnHelper::$promoGcReleased,
            'filters' => $request->only(['search', 'date'])
        ]);
    }

    public function viewReleased(PromoGcReleaseToDetail $id)
    {
        $rec = $id->load('user', 'promoGcRequest');

        $denom = PromoGcReleaseToItem::
            select(
                'promo_gc_release_to_items.prreltoi_barcode',
                'denomination.denomination'
            )
            ->join('gc', 'gc.barcode_no', '=', 'promo_gc_release_to_items.prreltoi_barcode')
            ->join('denomination', 'denomination.denom_id', '=', 'gc.denom_id')
            ->where('prreltoi_relid', $id->prrelto_id)->paginate(5);

        $total = PromoGcReleaseToItem::
            selectRaw(
                'SUM(denomination.denomination) as sum'
            )
            ->join('gc', 'gc.barcode_no', '=', 'promo_gc_release_to_items.prreltoi_barcode')
            ->join('denomination', 'denomination.denom_id', '=', 'gc.denom_id')
            ->where('prreltoi_relid', $id->prrelto_id)
            ->value('sum');

        $denom->transform(function ($item) {
            $item->denomination = NumberHelper::currency($item->denomination);
            return $item;
        });
        return response()->streamJson(['data' => $rec, 'denomination' => $denom, 'total' => NumberHelper::currency($total)]);

    }
}
