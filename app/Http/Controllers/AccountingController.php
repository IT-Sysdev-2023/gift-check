<?php

namespace App\Http\Controllers;

use App\DashboardClass;
use App\Helpers\ColumnHelper;
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
    public function setupPayment($id){

        return inertia('Accounting/Payment/SetupPayment', [
            'record' => $this->accountingservices->getDetialsEveryPayment($id),
        ]);
    }
    
    public function tableFetch($id){
        return $this->accountingservices->getDataList($id);
    }
}
