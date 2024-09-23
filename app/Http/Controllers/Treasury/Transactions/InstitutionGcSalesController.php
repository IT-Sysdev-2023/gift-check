<?php

namespace App\Http\Controllers\Treasury\Transactions;

use App\Helpers\ArrayHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\InstitutTransactionResource;
use App\Models\Assignatory;
use Illuminate\Database\Eloquent\Builder;
use App\Models\InstitutTransaction;
use App\Models\PaymentFund;
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
            // ->withSum('institutTransactionItem.gc as total_denomination', 'gc.denomination.denomination')
            ->orderByDesc('institutr_trnum')
            // ->limit(10)->get();
            ->paginate()
            ->withQueryString();


        // dd(InstitutTransactionResource::collection($data)->toArray($request));
        //   dd($data);
        // $table = 'institut_transactions_items';
        // $select = "IFNULL(SUM(denomination.denomination),0) as sum";
        // $where = "institut_transactions_items.instituttritems_trid='$tr->institutr_id'";
        // $join = 'INNER JOIN
        //         gc
        //     ON	
        //         gc.barcode_no = institut_transactions_items.instituttritems_barcode
        //     INNER JOIN
        //         denomination
        //     ON
        //         denomination.denom_id = gc.denom_id
        // ';
        // $limit = '';
        // $sum = getSelectedData($link,$table,$select,$where,$join,$limit);
        // echo number_format($sum->sum,2);

        return inertia('Treasury/Dashboard/InstitutionGcSales', [
            'title' => 'Institution Gc Sales Transactions',
            'data' => InstitutTransactionResource::collection($data),
            'columns' => ColumnHelper::$institution_gc_sales
        ]);
    }

    public function transactionDetails($id)
    {
        $record = InstitutTransaction::select(
            'institutr_id',
            'institutr_cusid',
            'institutr_trby',
            'institutr_trnum'
            ,
            'institutr_receivedby',
            'institutr_date',
            'institutr_remarks',
            'institutr_paymenttype'
        )
            ->where('institutr_id', $id)
            ->with([
                'institutCustomer:ins_id,ins_name',
                'institutPayment:insp_trid,institut_bankname,institut_bankaccountnum,institut_checknumber,institut_amountrec',
                'user:user_id,firstname,lastname',
                'institutTransactionItem',
                'document',
            ])
            ->first(); 

        // Separate query for paginating the relationship
        $institutTransactionItems = $record->institutTransactionItem()->with('gc.denomination')->paginate(5);

        return response()->json( ['details' => new InstitutTransactionResource($record), 'denominationTable' => $institutTransactionItems]);
    }
}
