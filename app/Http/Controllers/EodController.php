<?php

namespace App\Http\Controllers;

use App\Helpers\ColumnHelper;
use App\Models\StoreEod;
use App\Models\StoreVerification;
use App\Services\Eod\EodServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;

class EodController extends Controller
{
    public function __construct(public EodServices $eodServices) {}
    //
    public function index()
    {
        return inertia('Eod/EodDashboard');
    }
    public function eodVerifiedGc()
    {

        return inertia('Eod/VerifiedGc', [
            'record' => $this->eodServices->getVerifiedFromStore(),
            'columns' => ColumnHelper::$eod_columns,
        ]);
    }


    public function processEod(Request $request)
    {
        $storeod = StoreEod::create([
            'steod_by' => $request->user()->user_id,
            'steod_datetime' => now()
        ]);

        if ($storeod->wasRecentlyCreated) {
            return $this->eodServices->processEod($request, $storeod->steod_id);
        }
    }
}
