<?php

namespace App\Http\Controllers\Treasury\DtiTransaction;

use App\Helpers\NumberHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\DtiGcRequest as RequestsDtiGcRequest;
use App\Models\DtiDocument;
use App\Models\DtiGcRequest;
use App\Models\DtiGcRequestItem;
use App\Models\SpecialExternalCustomer;
use App\Models\SpecialExternalGcrequest;
use App\Services\DtiServices;
use App\Traits\DtiGcTraits;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class DtiTransactionController extends Controller
{
    use DtiGcTraits;
    //
    public function __construct(public DtiServices $dtiServices)
    {
    }
    public function index()
    {

        $spgcEx = SpecialExternalGcrequest::max('spexgc_num');
        $spgcExDti = DtiGcRequest::max('dti_num');

        if ($spgcEx > $spgcExDti) {
            $transactionNumber = $spgcEx;
        } else {
            $transactionNumber = $spgcExDti;
        }

        return inertia('Treasury/Dti/DtiIndex', [
            'dti' => self::options(),
            'transNo' => $transactionNumber ? NumberHelper::leadingZero($transactionNumber + 1, "%03d") : '0001',
        ]);
    }

    private function options()
    {

        return SpecialExternalCustomer::has('user')
            ->select('spcus_id as value', 'spcus_by', 'spcus_companyname as label', 'spcus_acctname as account_name')
            ->where('spcus_id', 342)
            ->first();
    }

    public function submitDtiForm(RequestsDtiGcRequest $request)
    {

        $dti = self::options();
        
        $request->validated();

        $dtiStore = $this->dtiServices->submissionForDti($request, $dti);

        $pdf = Pdf::loadView('pdf.dtirequest', ['data' => $dtiStore]);

        $pdf->setPaper('A3');

        $stream = base64_encode($pdf->output());

        return redirect()->back()->with(['stream' => $stream, 'success' => 'GC External Payment submission success']);
    }

    public function dtiPendingRequest()
    {
        return inertia('Treasury/Dti/DtiPendingRequest', [
            'records' => $this->getDtiPendingGcRequest(),
        ]);
    }

    public function dtiApprovedRequest(Request $request)
    {
        return inertia('Treasury/Dti/DtiApprovedRequestView', [
            'data' => $this->dtiApprovedRequestView($request),
            'searchValue' => $request->search,
        ]);
    }

    public function dtiApprovedView(Request $request)
    {
        // dd($this->dtiApprovedViewList($request));
        return inertia('Treasury/Dti/DtiApprovedView', [
            'data' => $this->dtiApprovedViewList($request),
        ]);
    }

    public function dtiEditRequest($id)
    {
        $record = DtiGcRequest::where('dti_num', $id)->first();

        $denom = DtiGcRequestItem::where('dti_trid', $record->dti_num)->get();

        $documents = DtiDocument::where('dti_trid', $record->dti_num)->get();

        $denom->transform(function ($item) {
            return (object) [
                'qty' => $item->dti_qty,
                'denomination' => $item->dti_denoms,
                'subtotal' => $item->dti_denoms * $item->dti_qty,
            ];
        });

        return inertia('Treasury/Dti/DtiEditPendingRequest', [
            'record' => $record,
            'dti' => self::options(),
            'total' => $denom->sum('subtotal'),
            'denom' => $denom,
            'docs' => $documents,
        ]);
    }
    public function dtiUpdateRequest(Request $request)
    {
        return $this->dtiServices->submitUpdateDtiRequest($request);
    }
}
