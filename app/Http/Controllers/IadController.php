<?php

namespace App\Http\Controllers;

use App\DashboardClass;
use App\Helpers\ColumnHelper;
use App\Models\TempValidation;
use App\Services\Iad\IadServices;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class IadController extends Controller
{

    public function __construct(public IadServices $iadServices, public DashboardClass $dashboardClass) {}

    public function index()
    {
        // numRowsWhereTwo($link,'special_external_gcrequest','spexgc_id','spexgc_status','spexgc_reviewed','approved','')

        return inertia('Iad/IadDashboard', [
            'count' => $this->dashboardClass->iadDashboard(),
            'budgetrequest' => $this->dashboardClass->budgetRequest(),
        ]);
    }

    public function setLimitAndTime()
    {
        ini_set('memory_limit', '1024M');
        set_time_limit(300);
    }

    public function receivingIndex()
    {
        return inertia('Iad/GcReceivingIndex', [
            'columns' => ColumnHelper::$receiving_columns,
            'record' =>  $this->iadServices->gcReceivingIndex(),

        ]);
    }

    public function setupReceiving(Request $request)
    {

        // dd($request->all());


        $data =  $this->iadServices->setupReceivingtxt($request);


        if (empty($data->requisFormDenom)) {

            return redirect()->back()->with([
                'status' => 'error',
                'msg' => 'Requistion Not Found!',
                'title' => 'Not Found!'
            ]);
        }

        return inertia('Iad/SetupReceiving', [
            'denomination' => $this->iadServices->getDenomination($data->requisFormDenom, $request),
            'scannedGc' => $this->iadServices->getScannedGc(),
            'columns' => ColumnHelper::$denomination_column,
            'record' => $data,
            'recnum' => $this->iadServices->getRecNum(),
            'reqid' => $request->requisId,
            'date' => today()->toFormattedDateString()
        ]);
    }

    public function validateByRange(Request $request)
    {
        return $this->iadServices->validateByRangeServices($request);
    }

    public function removeScannedGc(Request $request)
    {
        TempValidation::where('tval_barcode', $request->barcode)->delete();

        return back()->with([
            'status' => 'success',
            'title' => 'Success!',
            'msg' => 'Remove Barcode Successfully',
        ]);
    }
    public function validateBarcode(Request $request)
    {
        return $this->iadServices->validateBarcodeFunction($request);
    }

    public function submitSetup(Request $request)
    {
        // dd();
        return $this->iadServices->submitSetupFunction($request);
    }

    public function reviewedGcIndex(Request $request)
    {
        return inertia('Iad/ReviewedGc', [
            'record' => $this->iadServices->getReviewedGc($request),
            'columns' => ColumnHelper::$review_gc_columns,
        ]);
    }

    public function reviewDetails($id)
    {
        // dd();
        return inertia('Iad/ReviewDetails', [
            'record' => $this->iadServices->getReviewedDetails($id),
            'document' => $this->iadServices->getDocuments($id),
            'barcodes' => $this->iadServices->specialBarcodes($id),
            'approved' => $this->iadServices->approvedRequest($id),
        ]);
    }

    public  function receivedGc(Request $request)
    {
        // dd(1);
        return inertia('Iad/ReceivedGcIndex', [
            'record' => $this->iadServices->getReceivedGc($request),
            'columns' => ColumnHelper::$received_gc_index_columns,
        ]);
    }

    public function receivedGcDetails($id)
    {
        return response()->json([
            'record' => $this->iadServices->getReceivedDetails($id),
        ]);
    }
    public function approveBudget(Request $request, $id)
    {
        return $this->iadServices->updateBudgetRequest($request, $id);
    }
    public function details($id)
    {
        return response()->json([
            'record' => $this->iadServices->getDetails($id),
        ]);
    }

    public function auditStore(Request $request)
    {
        return inertia('Iad/AuditStore', [
            'record' => $this->iadServices->getAuditStore($request),
        ]);
    }
    public function auditStoreGenerate(Request $request)
    {
        return $this->iadServices->generateAudited($this->iadServices->getAuditStore($request));
    }
    public function verifiedSoldUsed(Request $request)
    {
        // dd($request->all());
        return inertia('Iad/VerifiedSoldUsedGc', [
            'record' => $this->iadServices->getVerifiedSoldUsedData($request)
        ]);
    }

    public function verifiedDetails($barcode)
    {
        return $this->iadServices->getVerifiedDetails($barcode);
    }
    public function verifiedsDetails($barcode)
    {
        return $this->iadServices->getVerifiedsDetails($barcode);
    }
    public function transactionTxtDetails($barcode)
    {
        return $this->iadServices->getTransactionText($barcode);
    }
    public function verifiedReports()
    {
        return inertia('Iad/Excel/VerifiedReports', [
            'stores' => $this->iadServices->getStores(),
        ]);
    }
    public function generateVerifiedReports(Request $request)
    {
        $this->setLimitAndTime();
        return $this->iadServices->generateVerifiedReportExcel($request);
    }

    public function purchasedReports()
    {
        return inertia('Iad/Excel/PurchasedReports', [
            'stores' => $this->iadServices->getStores(),
        ]);
    }
    public function generatePurchasedReportsExcel(Request $request)
    {
        $this->setLimitAndTime();
        return $this->iadServices->generatePurchasedReportsExcel($request);
    }
    public function generatePurchasedReports(Request $request)
    {
        // dd($request->all());
        $this->setLimitAndTime();
        return $this->iadServices->generatePurchasedReportsOpenOffice(collect($request));
    }

    public function specialReviewedReports(){
        return inertia('Iad/Excel/SpecialGcReviewedReports');
    }

    public function generateSpecialReports(Request $request){
        return $this->iadServices->generateSpecialReviewedReportsExcel($request);
    }

}
