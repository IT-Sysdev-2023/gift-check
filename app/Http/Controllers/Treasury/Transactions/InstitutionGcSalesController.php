<?php

namespace App\Http\Controllers\Treasury\Transactions;

use App\Http\Controllers\Controller;
use App\Http\Resources\InstitutTransactionResource;
use App\Models\Assignatory;
use App\Models\InstitutTransaction;
use App\Services\Treasury\ColumnHelper;
use App\Services\Treasury\Transactions\InstitutionGcSalesService;
use Illuminate\Http\Request;

class InstitutionGcSalesController extends Controller
{
    public function __construct(public InstitutionGcSalesService $institutionGcSalesService)
    {
    }
    public function index(Request $request)
    {
        $record = $this->institutionGcSalesService->index($request);

        return inertia('Treasury/Transactions/InstitutionGcSales/InstitutionSalesIndex', [
            'title' => 'Institution Gc Sales',
            'customer' => $record->customer,
            'paymentFund' => $record->paymentFund,
            'checkBy' => Assignatory::assignatories($request),
            'releasingNo' => $record->trNo,
            'scannedBc' => $record->scannedBarcode,
            'totalScannedDenomination' => collect($record->sessionBarcode)->sum('denomination'),
            'filters' => $request->only('date', 'search')
        ]);
    }

    public function scanBarcode(Request $request)
    {
        return $this->institutionGcSalesService->barcodeScanning($request);
    }

    public function removeBarcode(Request $request, $barcode)
    {
        return $this->institutionGcSalesService->destroyBarcode($request, $barcode);
    }

    public function formSubmission(Request $request)
    {
        return $this->institutionGcSalesService->store($request);
    }

    public function viewTransaction(Request $request)
    {
        $data = InstitutTransaction::select('institutr_cusid', 'institutr_id', 'institutr_trnum', 'institutr_paymenttype', 'institutr_receivedby', 'institutr_date')
            ->with(['institutCustomer:ins_id,ins_name', 'institutTransactionItem.gc.denomination'])
            ->withCount('institutTransactionItem')
            ->orderByDesc('institutr_trnum')
            ->paginate()
            ->withQueryString();

        return inertia('Treasury/Dashboard/InstitutionGcSales', [
            'title' => 'Institution Gc Sales Transactions',
            'data' => InstitutTransactionResource::collection($data),
            'columns' => ColumnHelper::$institution_gc_sales
        ]);
    }

    public function transactionDetails($id)
    {
        $data = $this->institutionGcSalesService->transactionDetails($id);
        return response()->json(['details' => new InstitutTransactionResource($data->record), 'denominationTable' => $data->denomination->paginate(5)]);
    }

    public function printAr($id){
        return $this->institutionGcSalesService->printAr($id);
    }

    public function reprint(Request $request, $id){
        return $this->institutionGcSalesService->reprint($request, $id);
    }

    public function generateExcel(Request $request, $id){
        return $this->institutionGcSalesService->excel($request, $id);
    }
}
