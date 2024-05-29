<?php

namespace App\Http\Controllers\Treasury;

use Illuminate\Routing\Controller;
use App\Services\Treasury\Masterfile;

class MasterfileController extends Controller
{
    public function customerSetup() //setup-tres-customer
    {
       return Masterfile::customerSetup();
    }

    public function specialExternalSetup() ///setup-special-external
    {
       return MasterFile::specialExternalSetup();
    }

    public function paymentFundSetup() //setup-paymentfund
    {
        return Masterfile::paymentFundSetup();
    }
}
