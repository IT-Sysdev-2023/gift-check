<?php

namespace App\Services\Accounting;

use App\Models\ApprovedRequest;
use App\Models\InstitutPayment;
use App\Models\SpecialExternalGcrequest;
use App\Models\SpecialExternalGcrequestEmpAssign;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AccountingServices
{

    public function __construct(public AccountingDbServices $dbservices) {}
    public function getPaymentList()
    {
        $data = SpecialExternalGcrequest::selectExternalRequestAll()
            ->join('special_external_customer', 'spcus_id', '=', 'spexgc_company')
            ->join('approved_request', 'reqap_trid', '=', 'spexgc_id')
            ->join('users as reqby', 'user_id', '=', 'spexgc_reqby')
            ->where('spexgc_released', 'released')
            ->orWhere('spexgc_reviewed', 'reviewed')
            ->where('reqap_approvedtype', 'special external releasing')
            ->orWhere('reqap_approvedtype', 'special external gc review')
            ->where('spexgc_payment_stat', '!=', 'FINAL')
            ->orWhere('spexgc_payment_stat', '!=', 'WHOLE')
            ->orderByDesc('spexgc_num')
            ->paginate(10)
            ->withQueryString();


        $data->transform(function ($item) {

            $item->reqby = Str::ucfirst($item->firstname) . ' ' . Str::ucfirst($item->lastname);
            $item->date = Date::parse($item->spexgc_datereq)->toFormattedDateString();
            $item->validity = Date::parse($item->spexgc_dateneed)->toFormattedDateString();

            return $item;
        });


        return $data;
    }

    public function getDetialsEveryPayment($id)
    {

        $data = SpecialExternalGcrequest::selectExternalRequestEvery()
            ->join('special_external_customer', 'spcus_id', '=', 'spexgc_company')
            ->join('approved_request', 'reqap_trid', '=', 'spexgc_id')
            ->join('users as reqby', 'reqby.user_id', '=', 'spexgc_reqby')
            ->join('users as prepby', 'prepby.user_id', '=', 'reqap_preparedby')
            ->join('access_page', 'prepby.usertype', '=', 'access_no')
            ->where('spexgc_id', $id)
            ->where('spexgc_status', 'approved')
            ->where('reqap_approvedtype', 'Special External GC Approved')
            ->where('spexgc_reviewed', 'reviewed')
            ->orderByDesc('spexgc_num')
            ->first();

        if ($data) {
            $data->datereq = Date::parse($data->spexgc_datereq)->toFormattedDateString();
            $data->timereq = Date::parse($data->spexgc_datereq)->format('h:i');
            $data->dateneeded = Date::parse($data->spexgc_dateneed)->toFormattedDateString();
            $data->dateapp = Date::parse($data->reqap_date)->toFormattedDateString();
            $data->reqby = Str::ucfirst($data->fn) . ' ' . Str::ucfirst($data->ln);
            $data->prepby = Str::ucfirst($data->firstname) . ' ' . Str::ucfirst($data->lastname);
        }

        return $data;
    }
    public function getDataList($id)
    {
        $data = SpecialExternalGcrequestEmpAssign::select(
            'spexgcemp_denom',
            'spexgcemp_fname',
            'spexgcemp_lname',
            'spexgcemp_mname',
            'spexgcemp_extname',
            'spexgcemp_barcode',
        )->where('spexgcemp_trid', $id)
            ->where('spexgc_status', '')
            ->orderBy('spexgcemp_id');

        return response()->json([
            'record' => $data->get(),
            'total' => $data->count(),
            'denomcount' => $data->sum('spexgcemp_denom'),
        ]);
    }

    public function submitPayementForm($request)
    {


        $request->validate([
            'payment' => 'required',
            'checkedby' => 'required',
            'receiveby' => 'required',
            'status' => 'required',
            'remarks' => 'required',
        ]);

        if ($request->payment === '0') {
            $request->validate([
                'amount' => 'required',
            ]);
        }

        if ($request->payment === '1') {
            $request->validate([
                'amount' => 'required',
                'checkno' => 'required',
                'bank' => 'required',
                'account' => 'required',
            ]);
        }

        if ($request->payement === '2') {
            $request->validate([
                'jv' => 'required',
            ]);
        }


        if (empty($request->checked)) {
            return back()->with([
                'status' => 'error',
                'msg' => 'Please Select Atleast One Record First!',
                'title' => 'Opps Error',
            ]);
        }


        if (SpecialExternalGcrequest::where('spexgc_payment_stat', '!=', 'FINAL')->where('spexgc_id', $request->id)->exists()) {

            $relid = $this->getSpecialGCReleasingNo();

            DB::transaction(function () use ($request, $relid) {

                $this->dbservices->insertIntoApprovedRequest($request, $relid)
                    ->insertIntoLedgerBudget($request)
                    ->updateSpecialEnternalGcRequest($request)
                    ->insertInstitutionalPayment($request)
                    ->updateSpecialExternalEmpAssign($request);
            });

            return redirect()->route('accounting.dashboard')->with([
                'status' => 'success',
                'msg' => 'Special External GC Transaction Saved.',
                'title' => 'Success',
            ]);
        } else {
            return back()->with([
                'status' => 'error',
                'msg' => 'Opss something went Wrong!!',
                'title' => 'Error',
            ]);
        }
    }
    private function getSpecialGCReleasingNo()
    {
        $data = ApprovedRequest::where('reqap_approvedtype', 'special external releasing')->orderByDesc('reqap_trnum')->max('reqap_trnum');

        if ($data > 0) {
            return $data++;
        }

        return 1;
    }

    public function getDonePayment()
    {
        $data = InstitutPayment::selectFilterInst()->join('special_external_gcrequest', 'spexgc_id', '=', 'insp_trid')
            ->join('special_external_customer', 'spcus_id', '=', 'spexgc_company')
            ->where('spexgc_payment_stat', '!=', 'pending')
            ->orderByDesc('insp_paymentnum')
            ->paginate(10)
            ->withQueryString();

        $data->transform(function ($item) {
            $item->institut_date = Date::parse($item->institut_date)->toFormattedDateString();
            return $item;
        });

        return $data;
    }
}
