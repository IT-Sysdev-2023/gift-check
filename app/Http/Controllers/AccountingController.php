<?php

namespace App\Http\Controllers;

use App\DashboardClass;
use App\Helpers\ColumnHelper;
use App\Models\SpecialExternalGcrequestEmpAssign;
use App\Services\Accounting\AccountingServices;
use Illuminate\Http\Request;

class AccountingController extends Controller
{
    public function __construct(public AccountingServices $accountingservices, public DashboardClass $dashboard) {}
    public function index()
    {
        return inertia('Accounting/AccountingDashboard', [
            'count' => $this->dashboard->accountingDashboard(),
        ]);
    }
    public function paymantGc()
    {
        return inertia('Accounting/Payment/PaymentIndex', [
            'record' => $this->accountingservices->getPaymentList(),
            'columns' => ColumnHelper::$payment_gc_columns
        ]);
    }
    public function setupPayment($id)
    {

        return inertia('Accounting/Payment/SetupPayment', [
            'record' => $this->accountingservices->getDetialsEveryPayment($id),
        ]);
    }

    public function tableFetch($id)
    {
        return $this->accountingservices->getDataList($id);
    }
    public function submitPayment(Request $request)
    {
        return $this->accountingservices->submitPayementForm($request);
    }
    public function paymentViewing()
    {
        return inertia('Accounting/Payment/PaymentViewing', [
            'record' => $this->accountingservices->getDonePayment(),
            'column' => ColumnHelper::$payment_viewing_column,
        ]);
    }
    public function paymentDetails($id)
    {
        $data = SpecialExternalGcrequestEmpAssign::select(
            'spexgcemp_denom',
            'spexgcemp_fname',
            'spexgcemp_lname',
            'spexgcemp_mname',
            'spexgcemp_barcode',
        )->where('payment_id', $id)
            ->orderByDesc('spexgcemp_barcode')
            ->get();

        $data->transform(function ($item) {
            $item->name = $item->spexgcemp_fname . ' ' . $item->spexgcemp_mname  . ' , ' . $item->spexgcemp_lname;
            return $item;
        });

        return $data;

    }
}
