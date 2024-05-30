<?php

namespace App\Http\Controllers\RetailStore;

use App\Http\Resources\retailStoreResource;
use App\Http\Resources\SoldGcResource;
use App\Models\Denomination;
use App\Models\StoreReceivedGc;
use App\Services\RetailStore\Dashboard\DashboardService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class MainController extends Controller
{
    public $retail;
    public function __construct(DashboardService $storeGcRequestService)
    {
        $this->retail = $storeGcRequestService;
    }
    public function index()
    {
        return [
            'storeGcRequest' => [
                'pending' => $this->retail->pendingGcRequest(),
                'released' => $this->retail->releasedGc(),
                'cancelled' => $this->retail->cancelledGcRequest(),

            ],
            'availableGc' => $this->retail->availableGc(),
            'soldGc' => $this->retail->soldGc()
        ];
    }

    public function viewAvailableGc(Request $request)
    {
        return $this->retail->viewAvailableGc($request);
    }

    public function viewSoldGc()
    {
        return SoldGcResource::collection($this->retail->viewSoldGc());
    }
}