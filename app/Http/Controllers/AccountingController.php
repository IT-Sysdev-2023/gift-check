<?php

namespace App\Http\Controllers;

use App\DashboardClass;
use App\Helpers\ColumnHelper;
use App\Models\SpecialExternalGcrequest;
use App\Models\SpecialExternalGcrequestEmpAssign;
use App\Models\Store;
use App\Services\Accounting\AccountingServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Inertia\Inertia;

class AccountingController extends Controller
{
    public function __construct(public AccountingServices $accountingservices, public DashboardClass $dashboard)
    {
    }
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

    public function tableFetchDtiTable($id)
    {
        return $this->accountingservices->getDataListDti($id);
    }

    public function submitPayment(Request $request)
    {
        return $this->accountingservices->submitPayementForm($request);
    }
    public function submitPaymentDti(Request $request)
    {
        return $this->accountingservices->submitPayementFormDti($request);
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
            $item->name = $item->spexgcemp_fname . ' ' . $item->spexgcemp_mname . ' , ' . $item->spexgcemp_lname;
            return $item;
        });

        return $data;

    }

    public function approvedGcRequest(Request $request)
    {
        $data = SpecialExternalGcrequest::with('specialExternalCustomer:spcus_id,spcus_acctname,spcus_companyname')
            ->selectFilterApproved()
            ->leftJoin('approved_request', 'reqap_trid', '=', 'spexgc_id')
            ->where('spexgc_promo', $request->promo ?? '0')
            ->where('spexgc_status', 'approved')
            ->where('reqap_approvedtype', 'Special External GC Approved')
            ->orderByDesc('spexgc_num')
            ->paginate(10)
            ->withQueryString();

        $data->transform(function ($item) {
            $item->company = $item->specialExternalCustomer->spcus_companyname;

            $item->datereq = Date::parse($item->spexgc_datereq)->toFormattedDateString();
            $item->dateneeded = Date::parse($item->spexgc_dateneed)->toFormattedDateString();
            $item->reqap_date = Date::parse($item->reqap_date)->toFormattedDateString();

            return $item;
        });

        return inertia('Custodian/ApprovedGcRequest', [
            'columns' => ColumnHelper::$approved_gc_column,
            'record' => $data
        ]);
    }

    public function billing_reports()
    {
        // dd();
        $store = Store::all();
        return Inertia::render('StoreAccounting/BillingReports', [
            'store' => $store
        ]);
    }

    public function paymantGcDti(){
        return inertia('Accounting/Payment/PaymentDtiIndex', [
            'records' => $this->accountingservices->getDtiList(),
        ]);
    }

    public function paymantGcDtiSetup($id){
        return inertia('Accounting/Payment/PaymentDtiSetup',[
            'record' => $this->accountingservices->getSingleListDti($id)
        ]);
    }
}
