<?php

namespace App\Services\Treasury\Transactions;

use App\Helpers\NumberHelper;
use App\Models\SpecialExternalCustomer;
use App\Services\Documents\UploadFileHandler;
use Illuminate\Http\Request;
use App\Models\SpecialExternalGcrequestItem;
use App\Rules\DenomQty;
use Illuminate\Support\Facades\DB;
use App\Models\SpecialExternalGcrequest;
use Illuminate\Support\Str;

class SpecialGcPaymentService extends UploadFileHandler
{
    public function __construct()
    {
        parent::__construct();
        $this->folderName = 'externalDocs';
    }
    public function store(Request $request)
    {
        $request->validate([
            'companyId' => 'required|exists:special_external_customer,spcus_id',
            'denomination' => ['required', 'array', new DenomQty()],
            'dateNeeded' => 'required|date',
            'remarks' => 'required',
            'paymentType.type' => 'required',
            'paymentType.amount' => [
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->input('paymentType.type') != 2 && (is_null($value) || $value == 0)) {
                        $fail('The ' . $attribute . ' is required and cannot be 0 if type is not 2.');
                    }
                },
            ]
        ], [
            'paymentType.type' => 'The payment type field is required.',
            'paymentType.amount' => 'The selected payment amount is required.'

        ]);

        return DB::transaction(function () use ($request) {

            $latestId = $this->segStore($request);

            $listOfDenom = $this->denominationStore($request, $latestId);

            //save scan image uploaded
            $this->saveMultiFiles($request, $latestId);

            return $this->dataForPdf($request, $listOfDenom);
        });

    }

    private function segStore(Request $request)
    {
        $gcPayment = $request->switchGc;
        $q = SpecialExternalGcrequest::create([
            'spexgc_num' => $request->trans,
            'spexgc_reqby' => $request->user()->user_id,
            'spexgc_datereq' => now(),
            'spexgc_dateneed' => $request->dateNeeded,
            'spexgc_remarks' => $request->remarks,
            'spexgc_company' => $request->companyId,
            'spexgc_payment' => $request->paymentType['amount'],
            'spexgc_paymentype' => $request->paymentType['type'],
            'spexgc_status' => 'pending',
            'spexgc_type' => 2,
            'spexgc_payment_stat' => 'paid',
            'spexgc_addemp' => 'pending',
            'spexgc_promo' => $gcPayment ? '*' : '0',
            'spexgc_payment_arnum' => $request->arNo
        ]);

        return $q->spexgc_id;
    }
    private function denominationStore(Request $request, $id)
    {
        $listOfDenom = collect($request->denomination);

        $listOfDenom->each(function ($denom) use ($id) {
            SpecialExternalGcrequestItem::create([
                'specit_denoms' => $denom['denomination'],
                'specit_qty' => $denom['qty'],
                'specit_trid' => $id
            ]);
        });

        return $listOfDenom;
    }

    private function dataForPdf(Request $request, $listOfDenom)
    {
        $company = SpecialExternalCustomer::select('spcus_companyname', 'spcus_acctname')->find($request->companyId);

        $amount = $listOfDenom->map(function ($item) {
            return $item['denomination'] * $item['qty'];
        })->sum();

        return [
            //Header
            'company' => [
                'name' => Str::upper('ALTURAS GROUP OF COMPANIES'),
                'department' => Str::title('Head Office - Treasury Department'),
                'report' => 'Institution GC Releasing Report',
            ],

            //SubHeader
            'subheader' => [
                'sgcReq' => $request->trans,
                'dateReceived' => today()->toFormattedDateString(),
                'customer' => $company->spcus_acctname,
                'accountName' => $company->spcus_companyname,
            ],

            'denom' => $listOfDenom,
            'totalGcQty' => $listOfDenom->sum('qty'),
            'totalGcAmount' => NumberHelper::format($amount),
            'receivedBy' => $request->user()->full_name
        ];
    }
}