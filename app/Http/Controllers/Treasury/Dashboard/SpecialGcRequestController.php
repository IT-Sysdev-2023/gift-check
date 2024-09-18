<?php

namespace App\Http\Controllers\Treasury\Dashboard;

use App\Helpers\NumberHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\SpecialExternalGcRequestResource;
use App\Models\SpecialExternalBankPaymentInfo;
use App\Models\SpecialExternalCustomer;
use App\Models\SpecialExternalGcrequest;
use App\Rules\DenomQty;
use App\Services\Treasury\ColumnHelper;
use App\Services\Treasury\Transactions\SpecialGcPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class SpecialGcRequestController extends Controller
{

    public function __construct(public SpecialGcPaymentService $specialGcPaymentService)
    {

    }
    public function pendingSpecialGc(Request $request)
    {
        $record = $this->specialGcPaymentService->pending();

        return inertia(
            'Treasury/Dashboard/SpecialGcTable',
            [
                'filters' => $request->only('search', 'date'),
                'title' => 'Special GC Request',
                'data' => SpecialExternalGcRequestResource::collection($record),
                'columns' => ColumnHelper::$pendingSpecialGc,
            ]
        );
    }
    public function updatePendingSpecialGc(SpecialExternalGcrequest $id)
    {
        $record = $id->load([
            'specialExternalCustomer',
            'specialExternalBankPaymentInfo',
            'document',
            'specialExternalGcrequestEmpAssign'
        ]);

        // dd(vars: $record->specialExternalGcrequestEmpAssign->groupBy('spexgcemp_denom'));
        return inertia(
            'Treasury/Dashboard/UpdateSpecialExternal',
            [
                'title' => 'Special GC Request',
                'data' => new SpecialExternalGcRequestResource($record),
                'assignedCustomer' => $record->specialExternalGcrequestEmpAssign->transform(function ($i) {
                    $i->spexgcemp_denom = (float) $i->spexgcemp_denom;
                    return $i;
                })->groupBy('spexgcemp_denom'),
                'options' => self::options()
            ]
        );
    }

    public function updateSpecialGc(Request $request)
    {
        return $this->specialGcPaymentService->updateSpecial($request);

    }

    //Special Gc Payment
    public function specialExternalPayment()
    {
        $transactionNumber = SpecialExternalGcrequest::max('spexgc_num');

        $transNo = $transactionNumber ?
            NumberHelper::leadingZero($transactionNumber + 1, "%03d")
            : '0001';

        return inertia('Treasury/Transactions/SpecialGcPayment/SpecialExtPayment', [
            'title' => 'Special External Gc Payment',
            'trans' => $transNo,
            'options' => self::options()
        ]);
    }

    public function gcPaymentSubmission(Request $request)
    {
        $data = $this->specialGcPaymentService->store($request);

        $pdf = Pdf::loadView('pdf.specialexternalpayment', ['data' => $data]);

        $pdf->setPaper('A3');

        $stream = base64_encode($pdf->output());

        return redirect()->back()->with(['stream' => $stream, 'success' => 'GC External Payment submission success']);
    }

    private function options()
    {
        return SpecialExternalCustomer::has('user')
            ->select('spcus_id as value', 'spcus_by', 'spcus_companyname as label', 'spcus_acctname as account_name')
            ->where('spcus_type', 2)
            ->orderByDesc('spcus_id')
            ->get();
    }
}
