<?php

namespace App\Services\Treasury;

use App\Http\Resources\InstitutCustomerResource;
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

    public static function specialExternalSetup() ///setup-special-external
    {
        //

        $record = SpecialExternalCustomer::with('user:user_id,firstname,lastname')
        ->select('spcus_by', 'spcus_id', 'spcus_companyname', 'spcus_acctname', 'spcus_address', 'spcus_cperson', 'spcus_cnumber', 'spcus_at')
        ->orderByDesc('spcus_id')
        ->cursorPaginate()
        ->withQueryString();

        return $record;

        //     $select = "special_external_customer.spcus_id, 
        //     special_external_customer.spcus_companyname, 
        //     special_external_customer.spcus_acctname,
        //     special_external_customer.spcus_address, 
        //     special_external_customer.spcus_cperson, 
        //     special_external_customer.spcus_cnumber, 
        //     special_external_customer.spcus_at,
        //     CONCAT(users.firstname,' ',users.lastname) as createdby";

        // $where = '1';

        // $join = 'INNER JOIN
        //     users
        // ON
        //     users.user_id = special_external_customer.spcus_by';

        // $limit ='ORDER BY spcus_id DESC';
        // $cus = getAllData($link,'special_external_customer',$select,$where,$join,$limit);
    }

    public static function paymentFundSetup() //setup-paymentfund
    {
        //
        $record = PaymentFund::with('user:user_id,firstname,lastname')
        ->select('pay_addby', 'pay_id', 'pay_desc', 'pay_status', 'pay_dateadded')
        ->where('pay_status', 'active')
        ->orderByDesc('pay_id')
        ->cursorPaginate()
        ->withQueryString();

        return $record;

        //     $table = 'payment_fund';
        // $select = "payment_fund.pay_id,
        //     payment_fund.pay_desc,
        //     payment_fund.pay_status,
        //     payment_fund.pay_dateadded,
        //     CONCAT(users.firstname,' ',users.lastname) as user";
        // $where = "payment_fund.pay_status='active'";
        // $join = 'INNER JOIN
        // 		users
        // 	ON
        // 		users.user_id = payment_fund.pay_addby	';
        // $limit = '';

        // $payment = getAllData($link,$table,$select,$where,$join,$limit);
    }
}