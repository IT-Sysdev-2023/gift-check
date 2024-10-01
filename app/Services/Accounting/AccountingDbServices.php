<?php

namespace App\Services\Accounting;

use App\Helpers\NumberHelper;
use App\Models\ApprovedRequest;
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

    private function getLedgerNumber()
    {
        $number = LedgerBudget::orderByDesc('bledger_id')->max('bledger_no') + 1;

        return NumberHelper::leadingZero($number, '%013d');
    }

    public function insertIntoLedgerBudget($request)
    {
        $denom = $this->totalEnternalRequest($request);

        $ledgerNumber = $this->getLedgerNumber();

        LedgerBudget::create([
            'bledger_no' =>  $ledgerNumber,
            'bledger_trid' => $request->id,
            'bledger_datetime' => now(),
            'bledger_type' => 'RFGCSEGCPAYMENT',
            'bdebit_amt' => $denom->total,
            'bledger_typeid' => '0',
            'bledger_group' => '0',
            'bcredit_amt' => '0.00',
            'btag' => '0',
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

        $instpayment = $this->instituteLedgerNumber() + 1;

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
}
