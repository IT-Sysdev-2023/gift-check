<?php

namespace App\Http\Controllers\Treasury\Transactions;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InstitutionGcSalesController extends Controller
{
    public function index(Request $request){
        // dd(1);
        return inertia('Treasury/Transactions/InstitutionGcSales/InstitutionSalesIndex', [
            'title' => 'Institution Gc Sales',
            // 'data' => PromoGcRequestResource::collection($records),
            // 'columns' => ColumnHelper::$promoGcReleasing,
            'filters' => $request->only('date', 'search')

        ]);
    }
}
