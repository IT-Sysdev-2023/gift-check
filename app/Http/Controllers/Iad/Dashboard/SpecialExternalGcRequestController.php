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
        return inertia('Iad/Dashboard/ApprovedGcTable', [
            'data' => SpecialExternalGcRequestResource::collection($data),
            'columns' => ColumnHelper::$approvedGcForReviewed,
            'filters' => $request->only('search', 'date')
        ]);
    }

    public function viewApprovedGcRecord(Request $request, SpecialExternalGcrequest $id)
    {

        $gcHolder = SpecialExternalGcrequestEmpAssign::where('spexgcemp_trid',$id->spexgc_id)->get();
        $col=ColumnHelper::$gcHolder;

        $record = $this->specialExternalGcService->viewApprovedGcRecord($request, $id);

        return inertia('Iad/Dashboard/ViewApprovedGcTable', [
            'data' => new SpecialExternalGcRequestResource($record),
            'title' => 'Special External Gc',
            'gcHolder' => $gcHolder,
            'gcholderCol' =>$col
        ]);

    }

    public function barcodeSubmission(Request $request, $id)
    {
        return $this->specialExternalGcService->barcodeScan($request, $id);
        //ajax.php search = gcreviewscangc
    }

    public function gcReview(Request $request, $id)
    {
        return $this->specialExternalGcService->review($request, $id);
        //ajax.php search = gcreview
    }

    public function reprint($id)
    {

        return $this->specialExternalGcService->reprint($id);
    }
}
