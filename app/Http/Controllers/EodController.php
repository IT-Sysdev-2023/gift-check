<?php

namespace App\Http\Controllers;

use App\Helpers\ColumnHelper;
use App\Http\Resources\InstitutPaymentResource;
use App\Jobs\EodScheduler;
use App\Models\InstitutEod;
use App\Models\InstitutPayment;
use App\Models\Store;
use App\Models\StoreEod;
use App\Models\StoreVerification;
use App\Services\Eod\EodServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

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


    public function eodVerifiedGc(Request $request)
    {
        return inertia('Eod/VerifiedGc', [
            'record' => $this->eodServices->getVerifiedFromStore($request),
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

    public function generatePdf(int $id)
    {
        return $this->eodServices->generatePdf($id);
    }

    public function gcSalesReport(Request $request)
    {
        $data = InstitutPayment::select('insp_id', 'insp_trid', 'insp_paymentcustomer', 'institut_bankname', 'institut_bankaccountnum', 'institut_checknumber', 'institut_amountrec', 'insp_paymentnum', 'institut_eodid')
            ->where('institut_eodid', '0')
            ->orderByDesc('insp_paymentnum')
            ->paginate()
            ->withQueryString();

        return inertia('Treasury/Dashboard/Eod/GcSalesReport', [
            'title' => 'Payment Transactions',
            'filters' => $request->only(['date', 'search']),
            'data' => InstitutPaymentResource::collection($data),
            'columns' => \App\Services\Treasury\ColumnHelper::$gcReleasingReport
        ]);
    }

    public function toEndOfDay()
    {
        EodScheduler::dispatch();
    }

    public function eodView(Request $request, $id)
    {
        $eod = $this->eodServices->getEodListDetails($request, $id);
        return inertia('Eod/EodDetails/EodListDetails', [
            'record' => $eod,
        ]);
    }

    public function eodViewDeodViewDetails($barcode)
    {
        return response()->json([
            'data' => $this->eodServices->getEodListDetailsTxt($barcode)
        ]);
    }
}
