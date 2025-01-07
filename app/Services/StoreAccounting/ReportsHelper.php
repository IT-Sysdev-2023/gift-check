<?php

namespace App\Services\StoreAccounting;

use App\DatabaseConnectionService;
use Illuminate\Support\Facades\DB;
class ReportsHelper
{

    public static function checkReveriedData($isLocal, $store, $year, $month)
    {
        $server = DatabaseConnectionService::getLocalConnection($isLocal, $store);

        return $server->table('store_verification')
            ->leftJoin('customers', 'customers.cus_id', '=', 'store_verification.vs_cn')
            ->whereYear('vs_date', $year)
            ->when(!is_null($month), fn($q) => $q->whereMonth('vs_date', $month))
            ->where('vs_store', $store)
            ->exists();
    }

    public static function checkRemoteDbReport($store, $year, $month, $isLocal)
    {
        $server = DatabaseConnectionService::getLocalConnection($isLocal, $store);
        return $server->table('store_eod_textfile_transactions')
            ->join('store_verification', 'store_verification.vs_barcode', '=', 'store_eod_textfile_transactions.seodtt_barcode')
            ->join('stores', 'stores.store_id', '=', 'store_verification.vs_store')
            ->leftJoin('customers', 'customers.cus_id', '=', 'store_verification.vs_cn')
            ->whereYear('vs_date', $year)
            ->when(!is_null($month), fn($q) => $q->whereMonth('vs_date', $month))
            ->where('vs_store', $store)
            ->exists();
    }

    public static function checkLocalDbBillingReport($store, $year, $month, $isLocal)
    {

        $server = DatabaseConnectionService::getLocalConnection($isLocal, $store);
        return $server->table('store_eod_textfile_transactions')
            ->join('transaction_sales', 'transaction_sales.sales_barcode', '=', 'store_eod_textfile_transactions.seodtt_barcode')
            ->join('transaction_stores', 'transaction_stores.trans_sid', '=', 'transaction_sales.sales_transaction_id')
            ->join('stores', 'stores.store_id', '=', 'transaction_stores.trans_store')
            ->join('store_verification', 'store_verification.vs_barcode', '=', 'transaction_sales.sales_barcode')
            ->leftJoin('customers', 'customers.cus_id', '=', 'store_verification.vs_cn')
            ->whereYear('vs_date', $year)
            ->when(!is_null($month), fn($q) => $q->whereMonth('vs_date', $month))
            ->where('trans_store', $store)
            ->whereRaw('stores.store_initial <> SUBSTRING(store_eod_textfile_transactions.seodtt_bu, 1, 5)')
            ->exists();
    }

    public static function checkRemoteDbBillingReport($isLocal, $store, $month, $year){
        $server = DatabaseConnectionService::getLocalConnection($isLocal, $store);
        return $server->table('store_verification')
            ->join('special_external_gcrequest_emp_assign', 'special_external_gcrequest_emp_assign.spexgcemp_barcode', '=', 'store_verification.vs_barcode')
            ->join('special_external_gcrequest', 'special_external_gcrequest.spexgc_id', '=', 'special_external_gcrequest_emp_assign.spexgcemp_trid')
            ->join('special_external_customer', 'special_external_customer.spcus_id', '=', 'special_external_gcrequest.spexgc_company')
            ->join('stores', 'stores.store_id', '=', 'store_verification.vs_store')
            ->join('store_eod_textfile_transactions', 'store_eod_textfile_transactions.seodtt_barcode', '=', 'store_verification.vs_barcode')
            ->join('customers', 'customers.cus_id', '=', 'store_verification.vs_cn')
            ->whereYear('vs_date', $year)
            ->when(!is_null($month), fn($q) => $q->whereMonth('vs_date', $month))
            ->where('vs_store', $store)
            ->where('special_external_gcrequest.spexgc_promo', '*')
            ->exists();
    }
}