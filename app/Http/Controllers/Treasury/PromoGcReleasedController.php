<?php

namespace App\Http\Controllers\Treasury;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PromoGcReleasedController extends Controller
{
    public function released(Request $request){
       return inertia('Treasury/Dashboard/PromoGcReleasing/PromoGcReleasingIndex', [
            'title' => 'Promo Gc Released',
            'record' => '',
            'filters' => $request->only(['search', 'date'])
       ]);
    }
}
