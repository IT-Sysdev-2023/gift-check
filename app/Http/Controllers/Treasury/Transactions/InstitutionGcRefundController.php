<?php

namespace App\Http\Controllers\Treasury\Transactions;

use App\Http\Controllers\Controller;
use App\Services\Treasury\Transactions\InstitutionGcRefundService;
use Illuminate\Http\Request;

class InstitutionGcRefundController extends Controller
{
    public function __construct(public InstitutionGcRefundService $institutionGcRefundService)
    {
    }
    public function index(Request $request)
    {

        return inertia('Treasury/Transactions/InstitutionGcRefund/InstitutionGcRefund');
    }

    public function store(Request $request)
    {
        
    }
}
