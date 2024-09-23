<?php

namespace App\Http\Controllers;

use App\Helpers\ColumnHelper;
use App\Models\InstitutEod;
use App\Models\StoreEod;
use App\Models\StoreVerification;
use App\Services\Eod\EodServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;

class EodController extends Controller
{
    public function __construct(public EodServices $eodServices)
    {
    }
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

    public function list()
    {
        return inertia('Eod/ListOfEod', [
            'record' => $this->eodServices->getEodList(),
        ]);
    }

    public function eodList(Request $request)
    {
       
        $data = InstitutEod::select('ieod_by', 'ieod_id', 'ieod_num', 'ieod_date')
            ->with('user:user_id,firstname,lastname')
            ->orderByDesc('ieod_date')
            ->filter($request)
            ->paginate()
            ->withQueryString();

        return inertia('Treasury/Dashboard/Eod/EodListTreasury', [
            'title' => 'Eod List',
            'filters' => $request->only(['date', 'search']),
            'data' => $data,
            'columns' => \App\Services\Treasury\ColumnHelper::$eodList
        ]);
    }

    public function generatePdf(int $id){
        return $this->eodServices->generatePdf($id);
    }
}
