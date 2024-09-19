<?php

namespace App\Services\Treasury\Transactions;

use App\Helpers\NumberHelper;
use App\Models\Document;
use App\Models\SpecialExternalCustomer;
use App\Services\Documents\UploadFileHandler;
use Illuminate\Http\Request;
use App\Models\SpecialExternalGcrequestEmpAssign;
use App\Models\SpecialExternalBankPaymentInfo;
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
            ->paginate()
            ->withQueryString();
    }
    public function store(Request $request)
    {
        $this->validateField($request);

        return DB::transaction(function () use ($request) {

            $latestId = $this->segStore($request);

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
                'required_if:paymentType.type,cash',
                'min:1',
                'gte:totalDenom',
                'nullable',
            ],
        ]);

        foreach ($request->defaultAssigned as $key => $values) {
            $assigned = $request->denom[$values['denomination']];
            if (count($assigned) != $values['qty']) {
                return redirect()->back()->with('error', 'Please Assign a employee');
            }
        }

        if (SpecialExternalCustomer::where('spcus_id', $request->customer['value'])->exists()) {
            $pType = $request->paymentType['type'] === 'Cash' ? 1 : 2;
            DB::transaction(function () use ($request, $pType) {
                SpecialExternalGcrequest::where([['spexgc_id', $request->reqid], ['spexgc_status', 'pending']])->update([
                    'spexgc_dateneed' => $request->dateValidity,
                    'spexgc_remarks' => $request->remarks,
                    'spexgc_payment_arnum' => $request->arNo,
                    'spexgc_company' => $request->customer['value'],
                    'spexgc_payment' => $request->paymentType['amount'],
                    'spexgc_paymentype' => $pType,
                    'spexgc_updatedby' => $request->user()->user_id,
                    'spexgc_updated_at' => now()
                ]);

                if ($request->paymentType['type'] === 'Cash') {
                    SpecialExternalBankPaymentInfo::where('spexgcbi_trid', $request->reqid)->delete();
                } else {
                    $check = SpecialExternalBankPaymentInfo::where('spexgcbi_trid', $request->reqid)->exists();

                    if ($check) {
                        if ($request->type == 1) {
                            SpecialExternalBankPaymentInfo::where('spexgcbi_trid', $request->reqid)->update([
                                'spexgcbi_bankname' => $request->paymentType['bankName'],
                                'spexgcbi_checknumber' => $request->paymentType['checkNumber'],
                                'spexgcbi_bankaccountnum' => $request->paymentType['accountNumber']
                            ]);
                        } elseif ($request->type == 2) {
                            SpecialExternalBankPaymentInfo::where('spexgcbi_trid', $request->reqid)->update([
                                'spexgcbi_bankname' => $request->paymentType['bankName'],
                                'spexgcbi_checknumber' => $request->paymentType['checkNumber']
                            ]);
                        }
                    } else {
                        SpecialExternalBankPaymentInfo::create([
                            'spexgcbi_trid' => $request->reqid,
                            'spexgcbi_bankname' => $request->paymentType['bankName'],
                            'spexgcbi_bankaccountnum' => $request->paymentType['accountNumber'],
                            'spexgcbi_checknumber' => $request->paymentType['checkNumber']
                        ]);
                    }
                }

                if ($request->type == 1) {
                    SpecialExternalGcrequestItem::where('specit_trid', $request->reqid)->delete();

                    foreach ($request->defaultAssigned as $key => $value) {

                        SpecialExternalGcrequestItem::insert([
                            'specit_denoms' => $value['denomination'],
                            'specit_qty' => $value['qty'],
                            'specit_trid' => $value['id']
                        ]);
                    }

                }

                if ($request->type == 2) {
                    SpecialExternalGcrequestEmpAssign::where('spexgcemp_trid', $request->reqid)->delete();

                    collect($request->denom)->values()->eachSpread(function ($item) use ($request) {
                        SpecialExternalGcrequestEmpAssign::create([
                            'spexgcemp_trid' => $request->reqid,
                            'spexgcemp_denom' => $item['spexgcemp_denom'],
                            'spexgcemp_fname' => $item['spexgcemp_fname'],
                            'spexgcemp_lname' => $item['spexgcemp_lname'],
                            'spexgcemp_mname' => $item['spexgcemp_mname'],
                            'spexgcemp_extname' => $item['spexgcemp_extname']
                        ]);
                    });
                }

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


    private function segStore(Request $request)
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
                    if ($request->input('paymentType.type') != 2 && (is_null($value) || $value == 0 || ($value < $request->input('total')))) {
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