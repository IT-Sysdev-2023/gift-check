<?php

namespace App\Services\RetailStore;

use App\Helpers\NumberHelper;
use App\Models\ApprovedGcrequest;
use App\Models\AppSetting;
use App\Models\Customer;
use App\Models\Denomination;
use App\Models\Gc;
use App\Models\GcRelease;
use App\Models\InstitutTransactionsItem;
use App\Models\LedgerCheck;
use App\Models\LedgerStore;
use App\Models\LostGcBarcode;
use App\Models\PromogcReleased;
use App\Models\SpecialExternalGcrequestEmpAssign;
use App\Models\Store;
use App\Models\StoreGcrequest;
use App\Models\StoreReceived;
use App\Models\StoreReceivedGc;
use App\Models\StoreVerification;
use App\Models\TempReceivestore;
use Illuminate\Support\Facades\Date;
use App\Services\RetailStore\RetailDbServices;
use Clue\Redis\Protocol\Model\Request;
use Illuminate\Support\Facades\DB;

class MarketingServices
{
    public function getPromoStatus(Request $request){

    }
}

