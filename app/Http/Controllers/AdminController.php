<?php

namespace App\Http\Controllers;

use App\Helpers\ColumnHelper;
use App\Http\Requests\PurchaseOrderRequest;
use App\Models\Gc;
use App\Models\PromoGcReleaseToItem;
use App\Models\SpecialExternalGcrequestEmpAssign;
use App\Services\Admin\AdminServices;
use App\Services\Admin\DBTransaction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function __construct(public AdminServices $adminservices, public DBTransaction $dBTransaction) {}

    public function index()
    {
        return inertia('Admin/AdminDashboard');
    }
    //
    public function statusScanner(Request $request)
    {
        $data = $this->adminservices->statusScanned($request);

        return Inertia::render('Admin/StatusScanner', [
            'data' => $data->steps,
            'latestStatus' => 0,
            'transType' => $data->transType,
            'statusBarcode' => $data->barcodeNotFound,
            'empty' => $data->empty,
            'success' => $data->success,
            'barcode' => $request->barcode,
            'fetch' => $request->fetch
        ]);
    }
    public function scanGcStatusIndex()
    {
        return Inertia::render('Admin/ScanGcStatuses');
    }
    public function barcodeStatus() {}



    public function purchaseOrderDetails()
    {
        return inertia('Admin/PurchaseOrderDetails', [
            'denomination' => $this->adminservices->denomination(),
            'supplier' => $this->adminservices->supplier(),
            'columns' => ColumnHelper::$purchase_details_columns,
            'record' => $this->adminservices->purchaseOrderDetails(),
        ]);
    }
    public function submitPurchaseOrders(PurchaseOrderRequest $request)
    {
        $denomination = collect($request->denom)->filter(function ($item) {
            return $item !== null;
        });

        return $this->dBTransaction->createPruchaseOrders($request, $denomination);
    }
}
