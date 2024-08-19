<?php

namespace App\Http\Controllers\Iad\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\SpecialExternalGcRequestResource;
use App\Models\ApprovedRequest;
use App\Models\SpecialExternalGcrequest;
use App\Models\SpecialExternalGcrequestEmpAssign;
use App\Models\SpecialExternalGcrequestItem;
use App\Services\Iad\DashboardService;
use App\Services\Iad\IadDashboardService;
use App\Services\Iad\IadSpecialExternalService;
use App\Services\Treasury\ColumnHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Number;
use Rmunate\Utilities\SpellNumber;

class SpecialExternalGcRequestController extends Controller
{

    public function __construct(public IadSpecialExternalService $iadSpecialExternalService)
    {
    }
    public function approvedGc(Request $request)
    {

        $data = $this->iadSpecialExternalService->specialExternalGcRequest();
        return inertia('Iad/Dashboard/ApprovedGcTable', [
            'data' => SpecialExternalGcRequestResource::collection($data),
            'columns' => ColumnHelper::$approvedGcForReviewed,
            'filters' => $request->only('search', 'date')
        ]);
    }

    public function viewApprovedGcRecord(SpecialExternalGcrequest $id)
    {
        $record = $this->iadSpecialExternalService->loadApprovedGc($id);
        return inertia('Iad/Dashboard/ViewApprovedGcTable', [
            'data' => new SpecialExternalGcRequestResource($record),
            'title' => 'Special External Gc'
        ]);

    }

    public function barcodeSubmission(Request $request, $id)
    {
        return $this->iadSpecialExternalService->barcodeScan($request, $id);
        //ajax.php search = gcreviewscangc
    }

    public function gcReview(Request $request, $id)
    {
       return $this->iadSpecialExternalService->gcSubmit($request, $id);
    }

    public function reprintGc($id){
        // $pdfContent = Storage
        // return response($pdfContent, 200)->header('Content-Type', 'application/pdf'); 
    }
}
