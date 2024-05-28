<?php

namespace App\Http\Controllers;

use App\Models\InstitutCustomer;
use App\Models\PaymentFund;
use App\Models\SpecialExternalCustomer;
use App\Services\MasterFile\Masterfile;
use Illuminate\Http\Request;

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
