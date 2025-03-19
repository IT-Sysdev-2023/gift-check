<?php

namespace App\Http\Controllers\Treasury\Transactions;

use App\Http\Controllers\Controller;
use App\Models\StoreGcrequest;
use App\Services\Treasury\ColumnHelper;
use Illuminate\Http\Request;

class RetailGcReleasingController extends Controller
{

    public function index(Request $request)
    {
        $record = StoreGcrequest::select('sgc_store', 'sgc_requested_by', 'sgc_id', 'sgc_num', 'sgc_date_needed', 'sgc_date_request', 'sgc_status')
            ->with('store:store_id,store_name', 'user:user_id,firstname,lastname')->where(function ($q) {
                $q->where('sgc_status', 1)
                    ->orWhere('sgc_status', 0);
            })->where('sgc_cancel', '')->orderByDesc('sgc_id')->paginate()->withQueryString();

        return inertia('Treasury/Transactions/RetailGcReleasing', [
            'title' => 'Retail Gc Releasing',
            'data' => $record,
            'columns' => ColumnHelper::$retailGcReleasing,
            'filters' => $request->only(['date', 'search'])
        ]);
    }
}
