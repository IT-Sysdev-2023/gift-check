<?php

namespace App\Http\Controllers\Treasury\Transactions;

use App\Http\Controllers\Controller;
use App\Http\Resources\PromoGcRequestResource;
use App\Services\Treasury\Transactions\PromoGcReleasingService;
use App\Services\Treasury\ColumnHelper;
use Illuminate\Http\Request;

class PromoGcReleasingController extends Controller
{

    public function __construct(public PromoGcReleasingService $promoGcReleasingService)
    {
    }
    public function index(Request $request)
    {
        $records = $this->promoGcReleasingService->index();

        return inertia('Treasury/Transactions/PromoGcReleasing/PromoGcReleasingIndex', [
            'title' => 'Promo Gc Releasing',
            'data' => PromoGcRequestResource::collection($records),
            'columns' => ColumnHelper::$promoGcReleasing,
            'filters' => $request->only('date', 'search')

        ]);
    }

    public function denominationList(Request $request, $id)
    {
        $record = $this->promoGcReleasingService->denominations($request, $id);
        return response()->json($record);
    }

    public function scanBarcode(Request $request)
    {
        return $this->promoGcReleasingService->barcodeScanning($request);
    }
    public function formSubmission(Request $request)
    {
        return $this->promoGcReleasingService->submit($request);

    }
}
