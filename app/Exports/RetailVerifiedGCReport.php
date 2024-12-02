<?php

namespace App\Exports;

use App\Events\verifiedgcreport;
use App\Models\StoreVerification;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;

class RetailVerifiedGCReport implements FromView
{
    /**
     * @return \Illuminate\Support\Collection
     */

    protected $request;

    public function __construct($requestdata)
    {
        $this->request = $requestdata;
    }
    public function view(): View
    {
        // Pass data to the Blade view
        return view('excel.verifiedGcReport', [
            'data' => $this->verifiedGCreport()
        ]);
    }

    public function verifiedGCreport()
    {

        $d1 = $this->request['date'][0];
        $d2 = $this->request['date'][1];

        $data = StoreVerification::select([
            'store_verification.vs_barcode',
            DB::raw("CONCAT(customers.cus_fname, ' ', customers.cus_lname) as customer"),
            DB::raw("CONCAT(users.firstname, ' ', users.lastname) as verby"),
            'store_verification.vs_tf_denomination',
            'store_verification.vs_tf_used',
            'store_verification.vs_gctype',
            'store_verification.vs_date',
            'store_verification.vs_reverifydate',
            'store_verification.vs_tf_balance',
        ])
            ->leftJoin('customers', 'customers.cus_id', '=', 'store_verification.vs_cn')
            ->leftJoin('users', 'users.user_id', '=', 'store_verification.vs_by')
            ->whereBetween(DB::raw("DATE_FORMAT(store_verification.vs_date, '%Y-%m-%d')"), [$d1, $d2])
            ->where('store_verification.vs_store', request()->user()->store_assigned)
            ->get();

        $count = $data->count();

        $no = 1;
        $data->transform(function ($item) use ($count, &$no) {
            verifiedgcreport::dispatch("Generating Pdf in progress.. ", $no++, $count, Auth::user());
            return $item;
        });

        return $data;

    }


}
