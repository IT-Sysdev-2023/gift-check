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

    public function specialExternalSetup() ///setup-special-external
    {
       return MasterFile::specialExternalSetup();
    }

    public function paymentFundSetup() //setup-paymentfund
    {
        return Masterfile::paymentFundSetup();
    }

    public function storeCustomer(Request $request){
        return MasterFile::storeCustomer($request);
    }
}
