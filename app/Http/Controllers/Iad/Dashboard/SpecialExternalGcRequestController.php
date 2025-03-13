<?php

namespace App\Http\Controllers\Iad\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\SpecialExternalGcRequestResource;
use App\Models\ApprovedRequest;
use App\Models\SpecialExternalGcrequest;
use App\Models\SpecialExternalGcrequestEmpAssign;
use App\Models\SpecialExternalGcrequestItem;
use App\Services\Iad\IadDashboardService;
use App\Services\Iad\SpecialExternalGcService;
use App\Services\Treasury\ColumnHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Number;
use Rmunate\Utilities\SpellNumber;

class SpecialExternalGcRequestController extends Controller
{

    public function __construct(public SpecialExternalGcService $specialExternalGcService)
    {
    }
    public function approvedGc(Request $request)
    {
        $data = $this->specialExternalGcService->approvedGc($request);
        // dd($data);
        return inertia('Iad/Dashboard/ApprovedGcTable', [
            'data' => SpecialExternalGcRequestResource::collection($data),
            'columns' => ColumnHelper::$approvedGcForReviewed,
            'filters' => $request->only('search', 'date')
        ]);
    }

    public function approvedDtiGc(Request $request)
    {
        $data = $this->specialExternalGcService->approvedDtiGc($request);

        return inertia('Iad/Dashboard/DtiApprovedView', [
            'data' => $data,
            'title' => 'Dti Gc Request View'
        ]);
    }

    public function viewDtiGc(Request $request)
    {
        $data = $this->specialExternalGcService->viewDtiGcData($request);
        // dd($data);
        return inertia('Iad/Dashboard/DtiApproved', [
            'columns' => ColumnHelper::$approvedDtiGcForReviewed,
            'data' => $data,
            'searchValue' => $request->search,
            'title' => 'Dti Gc Request List'
        ]);
    }

    public function dtiGcReviewed(Request $request)
    {
        $data = $this->specialExternalGcService->dtiGcReviewedList($request);
        // dd($data);
        return inertia('Iad/Dashboard/DtiGcReviewed', [
            'data' => $data,
            'columns' => ColumnHelper::$DtiGcReceived,
            'title' => 'Dti Gc Received List'
        ]);
    }

    public function viewApprovedGcRecord(Request $request, SpecialExternalGcrequest $id)
    {

        $gcHolder = SpecialExternalGcrequestEmpAssign::where('spexgcemp_trid', $id->spexgc_id)->get();
        $total = $gcHolder->count();
        $col = ColumnHelper::$gcHolder;

        $record = $this->specialExternalGcService->viewApprovedGcRecord($request, $id);

        return inertia('Iad/Dashboard/ViewApprovedGcTable', [
            'data' => new SpecialExternalGcRequestResource($record),
            'title' => 'Special External Gc',
            'gcHolder' => $gcHolder,
            'totalBarcode' => $total,
            'gcholderCol' => $col
        ]);

    }

    public function barcodeSubmission(Request $request, $id)
    {
        return $this->specialExternalGcService->barcodeScan($request, $id);
        //ajax.php search = gcreviewscangc
    }

    public function scanBarcode(Request $request){
        return $this->specialExternalGcService->dtiScanBarcode($request);
    }

    public function dtiReview(Request $request){
        return $this->specialExternalGcService->dti_review($request);
    }

    public function gcReview(Request $request, $id)
    {
    //   dd($request->all());
        return $this->specialExternalGcService->review($request, $id);
        //ajax.php search = gcreview
    }

    public function reprint($id)
    {

        return $this->specialExternalGcService->reprint($id);
    }


}
