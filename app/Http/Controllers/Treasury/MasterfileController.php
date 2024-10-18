<?php

namespace App\Http\Controllers\Treasury;

use Illuminate\Routing\Controller;
use App\Services\Treasury\Masterfile;
use Illuminate\Http\Request;

class MasterfileController extends Controller
{
    public function customerSetup(Request $request) //setup-tres-customer
    {
       return Masterfile::customerSetup($request);
    }

    public function specialExternalSetup(Request $request) ///setup-special-external
    {
       return MasterFile::specialExternalSetup($request);
    }

    public function paymentFundSetup(Request $request) //setup-paymentfund
    {
        return Masterfile::paymentFundSetup($request);
    }

    public function storeCustomer(Request $request){
        return MasterFile::storeCustomer($request);
    }

    public function storeSpecialExternalCustomer(Request $request){
        return MasterFile::storeSpecialExternalCustomer($request);
    }
}
