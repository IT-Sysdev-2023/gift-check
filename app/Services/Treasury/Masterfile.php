<?php

namespace App\Services\Treasury;

use App\Http\Resources\InstitutCustomerResource;
use App\Http\Resources\PaymentFundResource;
use App\Http\Resources\SpecialExternalCustomerResource;
use App\Models\InstitutCustomer;
use App\Models\PaymentFund;
use App\Models\SpecialExternalCustomer;
use Illuminate\Http\Request;

class Masterfile
{
    public static function customerSetup(Request $request) //setup-tres-customer
    {
        $record = InstitutCustomer::with('user:user_id,firstname,lastname', 'gcType:gc_type_id,gctype')
            ->select('ins_name', 'ins_custype', 'ins_by', 'ins_date_created', 'ins_gctype')
            ->where('ins_status', 'active')
            ->orderByDesc('ins_id')
            ->filter($request)
            ->paginate()
            ->withQueryString();

        return inertia('Treasury/Masterfile/CustomerSetup', [
            'title' => 'Customer Setup',
            'filters' => $request->only(['date', 'search']),
            'data' => InstitutCustomerResource::collection($record),
            'columns' => ColumnHelper::$customerSetup
        ]);
    }

    public static function specialExternalSetup(Request $request) ///setup-special-external
    {

        $record = SpecialExternalCustomer::with('user:user_id,firstname,lastname')
            ->select('spcus_by', 'spcus_id', 'spcus_companyname', 'spcus_acctname', 'spcus_address', 'spcus_cperson', 'spcus_cnumber', 'spcus_at')
            ->orderByDesc('spcus_id')
            ->filter($request)
            ->paginate()
            ->withQueryString();

        return inertia('Treasury/Masterfile/SpecialExternalGcSetup', [
            'title' => 'Special External Setup',
            'filters' => $request->only(['date', 'search']),
            'data' => SpecialExternalCustomerResource::collection($record),
            'columns' => ColumnHelper::$specialExternalSetup
        ]);
    }

    public static function paymentFundSetup(Request $request) //setup-paymentfund
    {
        $record = PaymentFund::with('user:user_id,firstname,lastname')
            ->select('pay_addby', 'pay_id', 'pay_desc', 'pay_status', 'pay_dateadded')
            ->where('pay_status', 'active')
            ->orderByDesc('pay_id')
            ->filter($request)
            ->paginate()
            ->withQueryString();

        return inertia('Treasury/Masterfile/PaymentFundSetup', [
            'title' => 'Payment Fund Setup',
            'filters' => $request->only(['date', 'search']),
            'data' => PaymentFundResource::collection($record),
            'columns' => ColumnHelper::$paymentFundSetup
        ]);
    }

    public static function storeCustomer(Request $request)
    {
        $request->validate([
            'customerName' => "required",
            'customerType' => "required",
            'gcType' => "required",
        ]);
        $isSuccess = InstitutCustomer::create([
            'ins_name' => $request->customerName,
            'ins_status' => 'active',
            'ins_custype' => $request->customerType,
            'ins_gctype' => $request->gcType,
            'ins_date_created' => now(),
            'ins_by' => $request->user()->user_id,
        ]);

        if ($isSuccess->wasRecentlyCreated) {
            return response()->json(['success' => 'Successfully Created'], 200);
        } else {
            return response()->json(['error' => 'Something Went Wrong']);
        }
    }

    public static function storeSpecialExternalCustomer(Request $request)
    {
        $request->validate([
            "company" => 'required',
            "customerType" => 'required',
            "accountName" => 'required',
            "address" => 'required',
            "contactPerson" => 'required',
            "contactNumber" => 'required'
        ]);

        $isSuccess = SpecialExternalCustomer::create([
            'spcus_companyname' => $request->company,
            'spcus_acctname' => $request->accountName,
            'spcus_address' => $request->address,
            'spcus_cperson' => $request->contactPerson,
            'spcus_cnumber' => $request->contactNumber,
            'spcus_at' => now(),
            'spcus_type' => $request->customerType,
            'spcus_by' => $request->user()->user_id,
        ]);

        if ($isSuccess->wasRecentlyCreated) {
            return response()->json(['success' => 'Successfully Created']);
        } else {
            return response()->json(['error' => 'Something Went Wrong']);
        }
    }

    public static function storePaymentFund(Request $request)
    {
        $request->validate([
            'paymentFundName' => 'required',
        ]);

        $isSuccess = PaymentFund::create([
            'pay_desc' => $request->paymentFundName,
            'pay_status' => 'active',
            'pay_dateadded' => now(),
            'pay_addby' => $request->user()->user_id,
        ]);
        if ($isSuccess->wasRecentlyCreated) {
            return response()->json(['success' => 'Successfully Created']);
        } else {
            return response()->json(['error' => 'Something Went Wrong']);
        }
    }
}