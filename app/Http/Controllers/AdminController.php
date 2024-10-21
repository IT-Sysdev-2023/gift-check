<?php

namespace App\Http\Controllers;

use App\Helpers\ColumnHelper;
use App\Http\Requests\PurchaseOrderRequest;
use App\Models\Denomination;
use App\Models\Gc;
use App\Models\PromoGcReleaseToItem;
use App\Models\RequisitionForm;
use App\Models\SpecialExternalGcrequestEmpAssign;
use App\Models\User;
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
        $users = User::count();
        return inertia('Admin/AdminDashboard', [
            'users' => $users
        ]);
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

    public function userlist()
    {

        $users = User::select(
            'users.user_id',
            'users.emp_id',
            'users.username',
            'users.firstname',
            'users.lastname',
            'users.login',
            'users.user_status',
            'users.date_created',
            'access_page.title',
            'stores.store_name'
        )
            ->join('access_page', 'users.usertype', '=', 'access_page.access_no')
            ->leftJoin('stores', 'users.store_assigned', '=', 'stores.store_id')
            ->orderByDesc('users.user_id')
            ->Paginate(10)
            ->withQueryString();
        $users->transform(function ($item) {

            $item->status = $item->user_status == 'active' ? true : false;
            return $item;
        });

        return Inertia::render('Admin/Masterfile/Users', [
            'users' => $users
        ]);
    }

    public function updateStatus(Request $request)
    {

        $user = User::where('user_id', $request->id)->first();

        if ($user) {
            User::where('user_id', $request->id)
                ->update([
                    'user_status' => $user['user_status'] == 'active' ? 'inactive' : 'active'
                ]);

            return back()->with([
                'type' => 'success',
                'msg' => 'Updated',
                'description' => 'Status updated successfully'
            ]);
        }
    }

    public function eodReports(Request $request)
    {
        return inertia('Admin/EodReports', [
            'record' => $this->adminservices->getEodDateRange($request),
            'stores' => $this->adminservices->stores(),
        ]);
    }

    public function generateReports(Request $request)
    {
        return $this->adminservices->generateReportPdf($request);
    }

    public function editPoDetails($id)
    {

        $data = RequisitionForm::with('requisFormDenom')->where('id', $id)->first();

        // $denom = [];

        // if ($data) {
        //     foreach ($data->requisFormDenom as $value) {

        //         $denom = Denomination::where('denom_fad_item_number', $value->denom_no)->get();

        //         if ($denom ?? 0) {
        //             $denom->qty = $value->quantity ?? 0;
        //         }
        //     }
        // }

        return response()->json([
            'record' => $data,
        ]);
    }

    public function setupPurchaseOrders($name){
        $data = $this->adminservices->getPoDetailsTextfiles($name);

        return inertia('Admin/SetupPurchaseOrders', [
            'record' =>  $data ,
            'denom' => $this->adminservices->getDenomination($data->denom),
            'title' => $name,
        ]);
    }
}
