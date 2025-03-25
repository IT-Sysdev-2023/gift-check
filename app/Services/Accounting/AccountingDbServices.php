<?php

namespace App\Services\Accounting;

use App\Helpers\NumberHelper;
use App\Models\ApprovedRequest;
use App\Models\DtiApprovedRequest;
use App\Models\DtiBarcodes;
use App\Models\DtiGcRequest;
use App\Models\DtiInstitutPayment;
use App\Models\InstitutPayment;
use App\Models\LedgerBudget;
use App\Models\SpecialExternalGcrequest;
use App\Models\SpecialExternalGcrequestEmpAssign;
use App\Models\SpecialExternalGcrequestItem;

class AccountingDbServices
{
    public function insertIntoApprovedRequest($request, $id)
    {
        ApprovedRequest::create([
            'reqap_trid' => $request->id,
            'reqap_approvedtype' => 'special external payment',
            'reqap_remarks' => $request->remarks,
            'reqap_preparedby' => $request->user()->user_id,
            'reqap_date' => now(),
            'reqap_trnum' => $id,
            'reqap_checkedby' => $request->checkedby,
            'reqap_doc' => '',
        ]);

        return $this;
    }
    private function totalEnternalRequest($request)
    {
        $reqtype = SpecialExternalGcrequest::where('spexgc_id', $request->id)->value('spexgc_type');

        if ($reqtype === '1') {

            $query = SpecialExternalGcrequestItem::where('specit_trid', $request->id);

            $query->get()->transform(function ($item) {
                $item->subtotal = $item->specit_denoms * $item->specit_qty;
                return $item;
            });

            return (object) [
                'total' => $query->sum('subtotal') ?? 0,
                'count' => $query->sum('specit_qty') ?? 0,
            ];
        } else {

            $query = SpecialExternalGcrequestEmpAssign::select('spexgcemp_denom')->where('spexgcemp_trid', $request->id);

            return (object) [
                'total' => $query->sum('spexgcemp_denom') ?? 0,
                'count' => $query->count() ?? 0,
            ];
        }
    }
    private function totalEnternalRequestDti($request)
    {
        $query = DtiBarcodes::select('dti_denom')->where('dti_trid', $request->id);

        return (object) [
            'total' => $query->sum('dti_denom') ?? 0,
            'count' => $query->count() ?? 0,
        ];
    }

    private function getLedgerNumber()
    {
        $number = LedgerBudget::orderByDesc('bledger_id')->max('bledger_no') + 1;

        return NumberHelper::leadingZero($number, '%013d');
    }

    public function insertIntoLedgerBudget($request)
    {
        $total = collect($request->checked)->sum('spexgcemp_denom');
        $ledgerNumber = $this->getLedgerNumber();

        LedgerBudget::create([
            'bledger_no' =>  $ledgerNumber,
            'bledger_trid' => $request->id,
            'bledger_datetime' => now(),
            'bledger_type' => 'RFGCSEGCPAYMENT',
            'bdebit_amt' => $total,
            'bledger_typeid' => '0',
            'bledger_group' => '0',
            'bcredit_amt' => '0.00',
            'btag' => '0',
            'bledger_category' => 'special'
        ]);

        return $this;
    }

    public function updateSpecialEnternalGcRequest($request)
    {


        SpecialExternalGcrequest::where('spexgc_id', $request->id)->update([
            'spexgc_amount' => $request->amount,
            'spexgc_balance' => $request->balance + -abs($request->amount),
            'spexgc_payment_stat' => $request->status,
        ]);

        return $this;
    }

    public function insertInstitutionalPayment($request)
    {
        $instpayment = 0;

        if ($this->instituteLedgerNumberDti() > $this->instituteLedgerNumber()) {
            $instpayment = $this->instituteLedgerNumberDti() + 1;
        } else {
            $instpayment = $this->instituteLedgerNumber() + 1;
        }


        if ($request->payment === '0') {

            InstitutPayment::create([
                'insp_paymentnum' => $instpayment,
                'insp_trid' => $request->id,
                'insp_paymentcustomer' => 'special external',
                'institut_amountrec' => $request->amount,
                'institut_eodid' => '0',
                'institut_date' => today()->format('Y-m-d'),
                'institut_jvcustomer' => 'Cash',
            ]);
        }

        if ($request->payment === '1') {

            InstitutPayment::create([
                'insp_paymentnum' => $instpayment,
                'insp_trid' => $request->id,
                'insp_paymentcustomer' => 'special external',
                'institut_bankname' => $request->bank,
                'institut_bankaccountnum' => $request->account,
                'institut_checknumber' => $request->checkno,
                'institut_amountrec' => $request->amount,
                'institut_eodid' => '0',
                'institut_date' => today()->format('Y-m-d'),
                'institut_jvcustomer' => 'Check',
            ]);
        }

        if ($request->payment === '2') {

            InstitutPayment::create([
                'insp_paymentnum' => $instpayment,
                'insp_trid' => $request->id,
                'insp_paymentcustomer' => 'special external',
                'institut_amountrec' => '0.00',
                'institut_eodid' => '0',
                'institut_date' => today()->format('Y-m-d'),
                'institut_jvcustomer' => $request->jv,
            ]);
        }

        return $this;
    }
    private function instituteLedgerNumber()
    {

        if (InstitutPayment::orderByDesc('insp_id')->max('insp_paymentnum') > 0) {

            return InstitutPayment::orderByDesc('insp_id')->max('insp_paymentnum');
        }

        return 1;
    }
    private function instituteLedgerNumberDti()
    {

        if (DtiInstitutPayment::orderByDesc('dti_insp_id')->max('dti_insp_paymentnum') > 0) {

            return DtiInstitutPayment::orderByDesc('dti_insp_id')->max('dti_insp_paymentnum');
        }

        return 1;
    }

    public function updateSpecialExternalEmpAssign($request)
    {
        $instpayment = $this->instituteLedgerNumber();

        collect($request->checked)->each(function ($item) use ($request,  $instpayment) {

            SpecialExternalGcrequestEmpAssign::where('spexgcemp_barcode', $item['spexgcemp_barcode'])
                ->where('spexgcemp_trid', $request->id)->update([
                    'spexgc_status' => '1',
                    'payment_id' => $instpayment,
                ]);
        });

        return $this;
    }
    public function insertIntoDtiApprovedRequest($request, $id)
    {
        DtiApprovedRequest::create([
            'dti_trid' => $request->id,
            'dti_approvedtype' => 'special external payment',
            'dti_remarks' => $request->remarks,
            'dti_preparedby' => $request->user()->user_id,
            'dti_date' => now(),
            'dti_trnum' => $id,
            'dti_checkby' => $request->checkedby,
            'dti_doc' => '',
        ]);

        return $this;
    }

    public function insertIntoLedgerBudgetDtiNew($request)
    {
        $total = collect($request->checked)->sum('dti_denom');

        $ledgerNumber = $this->getLedgerNumber();

        LedgerBudget::create([
            'bledger_no' =>  $ledgerNumber,
            'bledger_trid' => $request->id,
            'bledger_datetime' => now(),
            'bledger_type' => 'RFGCSEGCPAYMENT',
            'bdebit_amt' => $total,
            'bledger_typeid' => '0',
            'bledger_group' => '0',
            'bcredit_amt' => '0.00',
            'btag' => '0',
            'bcus_guide' => 'dti-new',
            'bledger_category' => 'special'
        ]);

        return $this;
    }

    public function updateDtiGcRequest($request)
    {
        DtiGcRequest::where('dti_num', $request->id)->update([
            'dti_amount' => $request->checkamount,
            'dti_balance' => $request->balance + -abs($request->checkamount),
            'dti_payment_stat' => $request->paymentstats,
        ]);

        return $this;
    }

    public function insertInstitutionalDtiPayment($request)
    {
    
        $instpayment = 0;

        if ($this->instituteLedgerNumberDti() > $this->instituteLedgerNumber()) {
            $instpayment = $this->instituteLedgerNumberDti() + 1;
        } else {
            $instpayment = $this->instituteLedgerNumber() + 1;
        }

        if ($request->paymentType === '1') {

            DtiInstitutPayment::create([
                'dti_insp_paymentnum' => $instpayment,
                'dti_insp_trid' => $request->id,
                'dti_status_pay' => $request->paymentstats,
                'dti_insp_paymentcustomer' => 'special external',
                'dti_institut_amountrec' => $request->amount,
                'dti_institut_eodid' => '0',
                'dti_institut_date' => today()->format('Y-m-d'),
                'dti_institut_jvcustomer' => 'Cash',
            ]);
        }

        if ($request->paymentType === '2') {

            DtiInstitutPayment::create([
                'dti_insp_paymentnum' => $instpayment,
                'dti_insp_trid' => $request->id,
                'dti_status_pay' => $request->paymentstats,
                'dti_insp_paymentcustomer' => 'special external',
                'dti_institut_bankname' => $request->bankname,
                'dti_institut_bankaccountnum' => $request->accountno,
                'dti_institut_checknumber' => $request->checkno,
                'dti_institut_amountrec' => $request->checkamount,
                'dti_institut_eodid' => '0',
                'dti_institut_date' => today()->format('Y-m-d'),
                'dti_institut_jvcustomer' => 'Check',
            ]);
        }

        if ($request->paymentType === '3') {

            DtiInstitutPayment::create([
                'dti_insp_paymentnum' => $instpayment,
                'dti_insp_trid' => $request->id,
                'dti_status_pay' => $request->paymentstats,
                'dti_insp_paymentcustomer' => 'special external',
                'dti_institut_amountrec' => '0.00',
                'dti_institut_eodid' => '0',
                'dti_institut_date' => today()->format('Y-m-d'),
                'dti_institut_jvcustomer' => $request->custname,
            ]);
        }

        return $this;
    }
    public function updateDtiBarcodes($request)
    {

        $instpayment = $this->instituteLedgerNumberDti();

        collect($request->checked)->each(function ($item) use ($request,  $instpayment) {

            DtiBarcodes::where('dti_barcode', $item['dti_barcode'])
                ->where('dti_trid', $request->id)->update([
                    'dti_status' => '1',
                    'payment_id' => $instpayment,
                ]);
        });

        return $this;
    }
}
