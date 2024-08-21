<?php

namespace App\Http\Controllers;

use App\Helpers\ColumnHelper;
use App\Models\Denomination;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RetailController extends Controller
{
    public function index()
    {
        return inertia('Retail/RetailDashboard');
    }

    public function gcRequest(Request $request)
    {
        $storeAssigned = $request->store_assigned;

        $denoms = Denomination::where('denom_type', 'RSGC')
            ->where('denom_status', 'active')->get();


        $denomColumns = array_map(
            fn($name, $field) => ColumnHelper::arrayHelper($name, $field),
            ['Denomination', 'Quantity'],
            ['denomination', 'qty']
        );


        return Inertia::render('Retail/RequestGc', [
            'denoms' => $denoms,
            'denomColumns' => ColumnHelper::getColumns($denomColumns)
        ]);
    }

    public function gcRequestsubmit(Request $request)
    {
        $denomination = collect($request->data['quantities'])->filter(function ($item) {
            return $item !== null;
        }); 

        dd($denomination);

    }
}
