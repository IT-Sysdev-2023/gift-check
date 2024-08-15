<?php

namespace App\Services\Treasury\Transactions;

use App\Services\Documents\UploadFileHandler;
use Illuminate\Http\Request;
use App\Models\SpecialExternalGcrequestItem;
use App\Rules\DenomQty;
use Illuminate\Support\Facades\DB;
use App\Models\SpecialExternalGcrequest;

class SpecialGcPaymentService extends UploadFileHandler
{
    public function __construct()
    {
        $this->folderName = 'externalDocs';
    }
    public function externalSubmission(Request $request)
    {
        // $request->dd();
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

        DB::transaction(function () use ($request) {
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
                'spexgc_promo' => '0',
                'spexgc_payment_arnum' => $request->arNo
            ]);

            $lastId = $q->spexgc_id;

            $denom = collect($request->denomination);

            $denom->each(function ($item) use ($lastId) {
                SpecialExternalGcrequestItem::create([
                    'specit_denoms' => $item->denomination,
                    'specit_qty' => $item->qty,
                    'specit_trid' => $lastId
                ]);
            });
        });
    }
}