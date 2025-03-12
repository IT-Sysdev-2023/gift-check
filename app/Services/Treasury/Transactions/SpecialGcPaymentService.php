<?php

namespace App\Services\Treasury\Transactions;

use App\Helpers\NumberHelper;
use App\Models\ApprovedRequest;
use App\Models\Document;
use App\Models\DtiApprovedRequest;
use App\Models\DtiBarcodes;
use App\Models\DtiGcRequest;
use App\Models\DtiLedgerSpgc;
use App\Models\LedgerBudget;
use App\Models\SpecialExternalCustomer;
use App\Services\Documents\FileHandler;
use Illuminate\Http\Request;
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

    public function pendingInternal()
    {
        return SpecialExternalGcrequest::with('user:user_id,firstname,lastname', 'specialExternalCustomer:spcus_id,spcus_acctname,spcus_companyname')
            ->select('spexgc_num', 'spexgc_reqby', 'spexgc_company', 'spexgc_dateneed', 'spexgc_id', 'spexgc_datereq')
            ->where([['special_external_gcrequest.spexgc_status', 'pending'], ['special_external_gcrequest.spexgc_promo', '*']])
            ->paginate()->withQueryString();
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

                $filter = collect($request->denomination)->reject(function ($item) {
                    return $item['denomination'] === 0 || $item['qty'] === 0;
                });

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

    public function releasingGc(string $promo)
    {
        return SpecialExternalGcrequest::with(['specialExternalCustomer:spcus_id,spcus_acctname,spcus_companyname', 'user:user_id,firstname,lastname', 'approvedRequestRevied.user', 'specialExternalGcrequestEmpAssign'])
            ->withWhereHas('approvedRequest', function ($q) {
                $q->select('reqap_trid', 'reqap_approvedby')->where('reqap_approvedtype', 'Special External GC Approved');
            })
            ->select('spexgc_reqby', 'spexgc_company', 'spexgc_id', 'spexgc_num', 'spexgc_dateneed', 'spexgc_id', 'spexgc_datereq')
            ->where([['spexgc_status', 'approved'], ['spexgc_reviewed', 'reviewed'], ['spexgc_released', ''], ['spexgc_promo', $promo]])
            ->orderByDesc('spexgc_id')
            ->paginate()
            ->withQueryString();
    }
    public function releasingGcDti()
    {
        $data = DtiGcRequest::with([
            'specialExternalCustomer:spcus_id,spcus_acctname,spcus_companyname',
            'user:user_id,firstname,lastname',
            'specialDtiBarcodesHasMany',
            'approvedRequestRevied.user',

        ])
            ->withWhereHas('approvedRequest', function ($q) {
                $q->select('dti_trid', 'dti_approvedby')->where('dti_approvedtype', 'Special External GC Approved');
            })
            ->select('dti_reqby', 'dti_company', 'dti_num', 'dti_num', 'dti_dateneed', 'id', 'dti_datereq')
            ->where([
                ['dti_status', 'approved'],
                ['dti_reviewed', 'reviewed'],
                ['dti_released', null],
                ['dti_promo', 'external']
            ])
            ->orderByDesc('id')
            ->paginate()
            ->withQueryString();


        $data->each(function ($item) {
            $q = collect($item->specialDtiBarcodesHasMany);

            $item->totalDenom = $q->sum('dti_denom');
            $item->customer = $item->specialExternalCustomer->spcus_acctname;
            $item->recby = $item->user->full_name;
            $item->approvedby = $item->approvedRequest?->dti_approvedby;
            $item->reviewedby = $item->approvedRequestRevied?->user->full_name;
            return $item;
        });

        return $data;
    }

    public function releasingDtiReviewed($id)
    {
        $data = DtiGcRequest::with([
            'specialExternalCustomer:spcus_id,spcus_acctname,spcus_companyname',
            'user:user_id,firstname,lastname',
            'specialDtiBarcodesHasMany',
            'approvedRequestRevied.user',
            'user.accessPage',
            'approvedRequest.user:user_id,firstname,lastname',

        ])
            ->withWhereHas('approvedRequest', function ($q) {
                $q->where('dti_approvedtype', 'Special External GC Approved');
            })
            ->where([
                ['dti_status', 'approved'],
                ['dti_reviewed', 'reviewed'],
                ['dti_released', null],
                ['dti_promo', 'external'],
                ['dti_num', $id],
            ])
            ->orderByDesc('id')
            ->first();

        if ($data) {
            $q = collect($data->specialDtiBarcodesHasMany);
            $data->totalDenom = NumberHelper::currency($q->sum('dti_denom'));
            $data->countBcode = $q->count();
            $data->customer = $data->specialExternalCustomer?->spcus_acctname;
            $data->recby = $data->user->full_name;
            $data->approvedby = $data->approvedRequest?->dti_approvedby;
            $data->reviewedby = $data->approvedRequestRevied?->user->full_name;
            $data->title = $data->user?->accessPage?->title;
            $data->apremarks = $data->approvedRequest->dti_remarks;
            $data->appdocs = $data->approvedRequest->dti_doc;
            $data->cby = $data->approvedRequest->user->full_name;
        }

        return $data;
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
                    if ((is_null($value))) {
                        $fail('The ' . $attribute . ' is required and cannot be 0 if type is not 2.');
                    }
                },
            ]

        ], [
            'paymentType.type' => 'The payment type field is required.',
            'paymentType.amount' => 'The selected payment amount is required.'

        ]);
    }

    // for dti releasing submit form

    public function dtiGcRequestUpdate($request, $id)
    {
        DtiGcRequest::where([["dti_num", $id], ['dti_released', null]])->update([
            'dti_released' => 'released',
            'dti_receivedby' => $request->receivedby
        ]);

        return $this;
    }
    public function dtiApprovedRequestCreate($request, $id)
    {
        $relid = DtiApprovedRequest::where('dti_approvedtype', 'special external releasing')->max('dti_trnum');

        DtiApprovedRequest::create([
            'dti_trid' => $id,
            'dti_approvedtype' => 'special external releasing',
            'dti_remarks' => $request->remarks,
            'dti_preparedby' => $request->user()->user_id,
            'dti_date' => now(),
            'dti_trnum' => $relid,
            'dti_checkby' => $request->checkedby
        ]);

        return $this;
    }
    public function ledgerBudgetCreate($id)
    {
        $q = DtiBarcodes::selectRaw("IFNULL(SUM(dti_barcodes.dti_denom),0.00) as totaldenom,
        IFNULL(COUNT(dti_barcodes.dti_denom),0) as cnt")->where('dti_trid', $id)->first();

        $l = LedgerBudget::max('bledger_id');

        $lnum = $l ? $l + 1 : 1;

        $total = $q->totaldenom;

        DtiLedgerSpgc::create([
            'dti_ledger_no' => $lnum,
            'dti_ledger_trid' => $id,
            'dti_ledger_datetime' => now(),
            'dti_ledger_type' => 'RFGCSEGCREL',
            'dti_ledger_debit' => $total,
        ]);

        return $this;
    }
}
