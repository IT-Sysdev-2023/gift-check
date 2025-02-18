<?php

namespace App\Http\Controllers\Treasury\DtiTransaction;

use App\Helpers\NumberHelper;
use App\Http\Controllers\Controller;
use App\Models\SpecialExternalCustomer;
use App\Models\SpecialExternalGcrequest;
use App\Services\DtiServices;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class DtiTransactionController extends Controller
{
    //
    public function __construct(public DtiServices $dtiServices) {}
    public function index()
    {

        $transactionNumber = SpecialExternalGcrequest::max('spexgc_num');

        return inertia('Treasury/Dti/DtiIndex', [
            'options' => self::options(),
            'transNo' => $transactionNumber ? NumberHelper::leadingZero($transactionNumber + 1, "%03d") : '0001',
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

    public function submitDtiForm(Request $request)
    {
        $dtiStore = $this->dtiServices->submissionForDti($request);

        $pdf = Pdf::loadView('pdf.dtirequest', ['data' => $dtiStore]);

        $pdf->setPaper('A3');

        $stream = base64_encode($pdf->output());

        return redirect()->back()->with(['stream' => $stream, 'success' => 'GC External Payment submission success']);
    }
}
