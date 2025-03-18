<?php

namespace App\Services\StoreAccounting;

use App\DatabaseConnectionService;
use App\Models\Store;
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

    public static function checkRemoteDbBillingReport($store, $year, $month, $isLocal)
    {
        // dd($store, $year, $month, $isLocal);
        $server = DatabaseConnectionService::getLocalConnection($isLocal, $store);
        return $server->table('store_eod_textfile_transactions')
            ->join('store_verification', 'store_verification.vs_barcode', '=', 'store_eod_textfile_transactions.seodtt_barcode')
            ->join('stores', 'stores.store_id', '=', 'store_verification.vs_store')
            ->leftJoin('customers', 'customers.cus_id', '=', 'store_verification.vs_cn')
            ->whereYear('vs_date', operator: $year)
            ->when(!is_null($month), fn($q) => $q->whereMonth('vs_date', $month))
            ->where('vs_store', $store)
            ->exists();
    }

    public static function checkLocalDbBillingReport($store, $year, $month, $isLocal)
    {

        $server = DatabaseConnectionService::getLocalConnection($isLocal, $store);
        // dd($server);

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


    public static function checkRedeemReport($isLocal, $store, $year, $month)
    {
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
            ->orderBy('vs_id')
            ->exists();
    }

    public static function checkBillingReportPerDayRemote($store, $date, $isLocal)
    {
        $server = DatabaseConnectionService::getLocalConnection($isLocal, $store);

        return $server->table('store_eod_textfile_transactions')
            ->join('store_verification', 'store_verification.vs_barcode', '=', 'store_eod_textfile_transactions.seodtt_barcode')
            ->join('stores', 'stores.store_id', '=', 'store_verification.vs_store')
            ->join('users', 'users.user_id', '=', 'store_verification.vs_by')
            ->leftJoin('customers', 'customers.cus_id', '=', 'store_verification.vs_cn')
            ->whereDate('vs_date', operator: $date)
            ->where('vs_store', $store)
            ->select(
                'store_eod_textfile_transactions.*',
                'store_name',
                'store_verification.*',
                'customers.cus_fname',
                'customers.cus_lname',
                'customers.cus_mname',
                'customers.cus_fname as vs_fullname',
                DB::raw('CONCAT(customers.cus_fname, " ", customers.cus_lname, " ",customers.cus_mname) as vs_fullname'),
                DB::raw('DATE_FORMAT(CONCAT(vs_date, " ", vs_time), "%Y-%m-%d %H:%i:%s" )as full_date'),
                DB::raw('CASE WHEN vs_gctype = 1 THEN "Regular"
                WHEN vs_gctype = 3 THEN "Special External"
                WHEN vs_gctype = 4 THEN "Promotional GC"
                WHEN vs_gctype = 6 THEN "Beam & Go"
                ELSE vs_gctype END as vs_gctype'),
                DB::raw('"Verified" as valid_type'),
                DB::raw('CONCAT(users.firstname, " ", users.lastname) as staff_name')

            )
            ->get();
    }

    public static function checkBillingReportPerDayLocal($store, $date, $isLocal)
    {
        $server = DatabaseConnectionService::getLocalConnection($isLocal, $store);
        $data = $server->table('store_eod_textfile_transactions')
            ->join('transaction_sales', 'transaction_sales.sales_barcode', '=', 'store_eod_textfile_transactions.seodtt_barcode')
            ->join('transaction_stores', 'transaction_stores.trans_sid', '=', 'transaction_sales.sales_transaction_id')
            ->join('stores', 'stores.store_id', '=', 'transaction_stores.trans_store')
            ->join('store_verification', 'store_verification.vs_barcode', '=', 'transaction_sales.sales_barcode')
            ->join('users', 'users.user_id', '=', 'store_verification.vs_by')
            ->leftJoin('customers', 'customers.cus_id', '=', 'store_verification.vs_cn')
            ->whereDate('vs_date', $date)
            ->where('trans_store', $store)
            ->whereRaw('stores.store_initial <> SUBSTRING(store_eod_textfile_transactions.seodtt_bu, 1, 5)')
            ->select(
                'store_eod_textfile_transactions.*',
                'store_name',
                'store_verification.*',
                'customers.cus_fname',
                'customers.cus_lname',
                'customers.cus_mname',
                'stores.store_name',
                DB::raw('CONCAT(customers.cus_fname, " ", customers.cus_lname, " ", customers.cus_mname) as vs_fullname'),
                DB::raw('DATE_FORMAT(CONCAT(vs_date, " ", vs_time), "%M %e, %Y" )as full_date'),
                DB::raw('CASE WHEN vs_gctype = 1 THEN "Regular"
                WHEN vs_gctype = 3 THEN "Special External"
                WHEN vs_gctype = 4 THEN "Promotional GC"
                WHEN vs_gctype = 6 THEN "Beam & Go"
                ELSE vs_gctype END as vs_gctype'),
                DB::raw('"Verified" as valid_type'),
                DB::raw('CONCAT(users.firstname, " ", users.lastname) as staff_name')
            )
            ->get();

        $data->transform(function ($item) {
            $item->store_name = Store::where('store_id', $item->vs_store)->value('store_name');
            // dd($store_name);
            return $item;
        });
        return $data;
    }
}
