<?php

namespace App\Services\Treasury\Transactions;

use App\Helpers\NumberHelper;
use App\Models\Document;
use App\Models\SpecialExternalCustomer;
use App\Services\Documents\FileHandler;
use Illuminate\Http\Request;
use App\Models\SpecialExternalGcrequestEmpAssign;
use App\Models\SpecialExternalBankPaymentInfo;
use App\Models\SpecialExternalGcrequestItem;
use App\Rules\DenomQty;
use Illuminate\Support\Facades\DB;
use App\Models\SpecialExternalGcrequest;
use Illuminate\Support\Str;

class SpecialGcPaymentService extends FileHandler
{
    public function __construct()
    {
        parent::__construct();
        $this->folderName = 'externalDocs';
    }

    public function pending()
    {
        return SpecialExternalGcrequest::with(
            'user:user_id,firstname,lastname',
            'specialExternalGcrequestItems:specit_trid,specit_denoms,specit_qty',
            'specialExternalCustomer:spcus_id,spcus_acctname,spcus_companyname'
        )
            ->select('spexgc_num', 'spexgc_dateneed', 'spexgc_id', 'spexgc_datereq', 'spexgc_company', 'spexgc_reqby')
            ->where([
                ['special_external_gcrequest.spexgc_status', 'pending'],
                ['special_external_gcrequest.spexgc_promo', '0']
            ])
            ->orderByDesc('spexgc_num')
            ->paginate()
            ->withQueryString();
    }
    public function store(Request $request)
    {
        $this->validateField($request);

        return DB::transaction(function () use ($request) {

            $latestId = $this->specialExternalStore($request);

            $listOfDenom = $this->denominationStore($request, $latestId);

            //save scan image uploaded
            $this->saveMultiFiles($request, $latestId, function ($id, $path) use ($request) {
                $gcMode = $request->switchGc ? 'Special Internal GC Request' : 'Special External GC Request';
                Document::create([
                    'doc_trid' => $id,
                    'doc_type' => $gcMode,
                    'doc_fullpath' => $path
                ]);
            });

            return $this->dataForPdf($request, $listOfDenom);
        });

    }

    public function updateSpecial(Request $request)
    {
        $request->validate([
            'dateValidity' => 'required',
            'remarks' => 'required',
            'paymentType.amount' => [
                'required',
                'min:1',
                'gte:totalDenom',
                'nullable',
            ],
            'denomination' => ['required', 'array', new DenomQty()],
            'paymentType.bankName' => 'required_if:paymentType.type,2',
            'paymentType.accountNumber' => 'required_if:paymentType.type,2',
            'paymentType.checkNumber' => 'required_if:paymentType.type,2',
        ]);

        if (SpecialExternalCustomer::where('spcus_id', $request->customer['value'])->exists()) {

            DB::transaction(function () use ($request) {

                SpecialExternalGcrequest::where([['spexgc_id', $request->reqid], ['spexgc_status', 'pending']])->update([
                    'spexgc_dateneed' => $request->dateValidity,
                    'spexgc_remarks' => $request->remarks,
                    'spexgc_payment_arnum' => $request->arNo,
                    'spexgc_company' => $request->customer['value'],
                    'spexgc_payment' => $request->paymentType['amount'],
                    'spexgc_paymentype' => $request->paymentType['type'],
                    'spexgc_updatedby' => $request->user()->user_id,
                    'spexgc_updated_at' => now()
                ]);

                if ($request->paymentType['type'] == '1') { //Cash
                    SpecialExternalBankPaymentInfo::where('spexgcbi_trans_id', $request->reqid)->delete();
                } else {
                    $check = SpecialExternalBankPaymentInfo::where('spexgcbi_trans_id', $request->reqid)->exists();

                    if ($check) {
                        SpecialExternalBankPaymentInfo::where('spexgcbi_trans_id', $request->reqid)->update([
                            'spexgcbi_bankname' => $request->paymentType['bankName'],
                            'spexgcbi_checknumber' => $request->paymentType['checkNumber'],
                            'spexgcbi_bankaccountnum' => $request->paymentType['accountNumber']
                        ]);
                    } else {
                        SpecialExternalBankPaymentInfo::create([
                            'spexgcbi_trans_id' => $request->reqid,
                            'spexgcbi_bankname' => $request->paymentType['bankName'],
                            'spexgcbi_bankaccountnum' => $request->paymentType['accountNumber'],
                            'spexgcbi_checknumber' => $request->paymentType['checkNumber']
                        ]);
                    }
                }

                SpecialExternalGcrequestItem::where('specit_trid', $request->reqid)->delete();
                $filter = collect($request->denomination)->reject(function ($item){ return $item['denomination'] === 0 || $item['qty'] === 0;});
                $filter->each(function ($val) use ($request) {
                        SpecialExternalGcrequestItem::create([
                            'specit_denoms' => $val['denomination'],
                            'specit_qty' => $val['qty'],
                            'specit_trid' => $request->reqid,
                        ]);
                });

                if ($request->has('file')) {
                    $documents = Document::where([['doc_type', 'Special External GC Request'], ['doc_trid', $request->reqid]]);

                    if ($documents->exists()) {
                        $documents->delete();
                    }
                }

                $this->saveMultiFiles($request, $request->reqid, function ($id, $path) {

                    Document::create([
                        'doc_trid' => $id,
                        'doc_type' => 'Special External GC Request',
                        'doc_fullpath' => $path
                    ]);
                });


            });
            return redirect()->back()->with('success', 'Successfully Updated!');
        } else {
            return redirect()->back()->with('error', 'Company dont Exists!');
        }
    }


    private function specialExternalStore(Request $request)
    {
        //external = false
        //internal = true
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

        //if the payment type is Check
        if ($request->paymentType['type'] == 2) {
            SpecialExternalBankPaymentInfo::create(attributes: [
                'spexgcbi_trans_id' => $q->spexgc_id,
                'spexgcbi_bankname' => $request->paymentType['bankName'],
                'spexgcbi_bankaccountnum' => $request->paymentType['accountNumber'],
                'spexgcbi_checknumber' => $request->paymentType['checkNumber']
            ]);
        }

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
        $gcMode = $request->switchGc ? 'Special Internal Request Report' : 'Special External Request Report';
        $company = SpecialExternalCustomer::select('spcus_companyname', 'spcus_acctname')->find($request->companyId);

        $amount = $listOfDenom->map(function ($item) {
            return $item['denomination'] * $item['qty'];
        })->sum();

        return [
            //Header
            'company' => [
                'name' => Str::upper('ALTURAS GROUP OF COMPANIES'),
                'department' => Str::title('Head Office - Treasury Department'),
                'report' => $gcMode,
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


    private function validateField(Request $request)
    {

        $request->validate([
            'companyId' => 'required|exists:special_external_customer,spcus_id',
            'denomination' => ['required', 'array', new DenomQty()],
            'dateNeeded' => 'required|date',
            'remarks' => 'required',
            'file' => 'required',

            //Check PaymentType
            'paymentType.bankName' => 'required_if:paymentType.type,2',
            'paymentType.accountNumber' => 'required_if:paymentType.type,2',
            'paymentType.checkNumber' => 'required_if:paymentType.type,2',

            'paymentType.type' => 'required',
            'paymentType.amount' => [
                function ($attribute, $value, $fail) use ($request) {
                    if ((is_null($value) || $value == 0 || ($value < $request->input('total')))) {
                        $fail('The ' . $attribute . ' is required and cannot be 0 if type is not 2.');
                    }
                },
            ]

        ], [
            'paymentType.type' => 'The payment type field is required.',
            'paymentType.amount' => 'The selected payment amount is required.'

        ]);
    }
}