<?php

namespace App\Http\Controllers\Treasury\Transactions;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InstitutionGcRefundController extends Controller
{
    public function index(Request $request)
    {
        return inertia('Treasury/Transactions/InstitutionGcRefund/InstitutionGcRefund',[
            'title' => 'Institution Gc Refund',
            'data' => [],
            'column' => []
        ]);
    }

    public function store(Request $request)
    {
        
    }
}
