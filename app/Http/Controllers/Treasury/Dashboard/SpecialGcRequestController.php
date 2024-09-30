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

        $externalGc = SpecialExternalGcrequest::with('user:user_id,firstname,lastname', 'specialExternalCustomer:spcus_id,spcus_acctname,spcus_companyname')
            ->select('spexgc_num', 'spexgc_reqby', 'spexgc_company', 'spexgc_dateneed', 'spexgc_id', 'spexgc_datereq')
            ->where([['special_external_gcrequest.spexgc_status', 'pending'], ['special_external_gcrequest.spexgc_promo', '*']])
            ->paginate()->withQueryString();
        return inertia(
            'Treasury/Dashboard/SpecialGcTable',
            [
                'filters' => $request->only('search', 'date'),
                'title' => 'Special GC Request',
                'data' => SpecialExternalGcRequestResource::collection($record),
                'externalData' => SpecialExternalGcRequestResource::collection($externalGc),
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
    public function specialGcPayment()
    {
        $transactionNumber = SpecialExternalGcrequest::max('spexgc_num');

        $transNo = $transactionNumber ?
            NumberHelper::leadingZero($transactionNumber + 1, "%03d")
            : '0001';

        return inertia('Treasury/Transactions/SpecialGcPayment/SpecialExtPayment', [
            'title' => 'Gc Payment',
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

    public function pendingInternalGc()
    {
        $data = SpecialExternalGcrequest::with('user:user_id,firstname,lastname', 'specialExternalCustomer:spcus_id,spcus_acctname,spcus_companyname')
            ->select('spexgc_num', 'spexgc_reqby', 'spexgc_company', 'spexgc_dateneed', 'spexgc_id', 'spexgc_datereq')
            ->where([['special_external_gcrequest.spexgc_status', 'pending'], ['special_external_gcrequest.spexgc_promo', '*']])
            ->paginate()->withQueryString();
    }

    public function releasingInternal(Request $request)
    {
        $record = SpecialExternalGcrequest::
            with(['specialExternalCustomer:spcus_id,spcus_acctname,spcus_companyname', 'user:user_id,firstname,lastname', 'approvedRequestRevied.user'])
            ->withWhereHas('approvedRequest', function ($q) {
                $q->select('reqap_trid', 'reqap_approvedby')->where('reqap_approvedtype', 'Special External GC Approved');
            })
            ->select('spexgc_reqby', 'spexgc_company', 'spexgc_id', 'spexgc_num', 'spexgc_dateneed', 'spexgc_id', 'spexgc_datereq')
            ->where([['spexgc_status', 'approved'], ['spexgc_reviewed', 'reviewed'], ['spexgc_released', ''], ['spexgc_promo', '*']])
            ->paginate()
            ->withQueryString();

        return inertia('Treasury/Dashboard/SpecialExternalGc/ReviewedReleasingInternal', [
            'title' => 'Reviewed GC For Releasing(Internal)',
            'records' => SpecialExternalGcRequestResource::collection($record),
            'columns' => ColumnHelper::$specialInternal
        ]);
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
