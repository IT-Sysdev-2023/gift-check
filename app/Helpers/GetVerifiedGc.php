<?php

namespace App\Helpers;

use App\Models\StoreVerification;
use Illuminate\Support\Facades\DB;

class GetVerifiedGc
{
    public static function getVerifiedGc($id, $search)
{
    return StoreVerification::leftJoin('stores', 'stores.store_id', '=', 'store_verification.vs_store')
        ->leftJoin('users as revby', 'revby.user_id', '=', 'store_verification.vs_reverifyby')
        ->leftJoin('users as verby', 'verby.user_id', '=', 'store_verification.vs_by')
        ->leftJoin('customers', 'customers.cus_id', '=', 'store_verification.vs_cn')
        ->where('store_verification.vs_store', $id)
        ->where(function ($query) use ($search) {
            $query->where('store_verification.vs_barcode', 'LIKE', '%' . $search . '%')
                  ->orWhere('store_verification.vs_tf_denomination', 'LIKE', '%' . $search . '%');
        })
        ->select(
            'store_verification.vs_barcode',
            'store_verification.vs_tf_denomination',
            DB::raw("CONCAT(UCASE(LEFT(customers.cus_lname, 1)), LCASE(SUBSTRING(customers.cus_lname, 2))) as customersLastname"),
            DB::raw("CONCAT(UCASE(LEFT(customers.cus_fname, 1)), LCASE(SUBSTRING(customers.cus_fname, 2))) as customersFirstname"),
            DB::raw("CONCAT(UPPER(SUBSTRING(verby.firstname, 1, 1)), LOWER(SUBSTRING(verby.firstname, 2))) as verbyFirstname"),
            DB::raw("CONCAT(UPPER(SUBSTRING(verby.lastname, 1, 1)), LOWER(SUBSTRING(verby.lastname, 2))) as verbyLastname"),
            DB::raw("CONCAT(UPPER(SUBSTRING(revby.firstname, 1, 1)), LOWER(SUBSTRING(revby.firstname, 2))) as revbyFirstname"),
            DB::raw("CONCAT(UPPER(SUBSTRING(revby.lastname, 1, 1)), LOWER(SUBSTRING(revby.lastname, 2))) as revbyLastname"),
            'store_verification.vs_tf_used',
            'store_verification.vs_tf_balance',
            'store_verification.vs_date',
            'store_verification.vs_time',
            'store_verification.vs_reverifydate'
        )
        ->paginate(10)
        ->withQueryString();
}

}
