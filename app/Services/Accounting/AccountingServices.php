<?php

namespace App\Services\Accounting;

use App\Models\ApprovedRequest;
use App\Models\DtiApprovedRequest;
use App\Models\DtiBarcodes;
use App\Models\DtiGcRequest;
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


    public function getDtiList()
    {
        $data =  DtiGcRequest::select(
            'dti_num',
            'dti_datereq',
            'dti_dateneed',
            'firstname',
            'lastname',
            'spcus_acctname',
            'dti_payment_stat',
            'dti_balance',
        )
            ->join('special_external_customer', 'spcus_id', '=', 'dti_company')
            ->join('dti_approved_requests', 'dti_trid', '=', 'dti_gc_requests.dti_num')
            ->join('users as reqby', 'user_id', '=', 'dti_reqby')
            ->where([
                ['dti_released', 'released'],
                ['dti_approvedtype', 'special external gc released'],
                ['dti_payment_stat', '!=', 'FINAL'],
            ])->orderByDesc('dti_gc_requests.id')
            ->get();

        $data->each(function ($item) {
            $item->name = Str::ucfirst($item->firstname) . ', ' . Str::ucfirst($item->lastname);
            return $item;
        });

        return $data;
    }

    public function getSingleListDti($id)
    {
        $data = DtiGcRequest::select(
            'dti_num',
            'title',
            'dti_datereq',
            'dti_dateneed',
            'dti_approveddate',
            'dti_balance',
            'dti_approved_requests.dti_remarks as apremarks',
            'dti_gc_requests.dti_remarks as remarks',
            'dti_checkby',
            'dti_gc_requests.dti_approvedby',
            'reqby.firstname as refirstname',
            'reqby.lastname as relastname',
            'prepby.firstname as prefirstname',
            'prepby.lastname as prelastname',
        )->join('special_external_customer', 'spcus_id', '=', 'dti_company')
            ->join('dti_approved_requests', 'dti_approved_requests.dti_trid', '=', 'dti_gc_requests.dti_num')
            ->join('users as reqby', 'reqby.user_id', '=', 'dti_reqby')
            ->join('users as prepby', 'prepby.user_id', '=', 'dti_preparedby')
            ->join('access_page', 'prepby.usertype', '=', 'access_no')
            ->where([
                ['dti_num', $id],
                ['dti_status', 'approved'],
                ['dti_approvedtype', 'Special External GC Approved'],
                ['dti_reviewed', 'reviewed'],
            ])
            ->first();

        if ($data) {
            $data->prefullname = Str::ucfirst($data->prefirstname) . ', ' . Str::ucfirst($data->prelastname);
            $data->refullname = Str::ucfirst($data->refirstname) . ', ' . Str::ucfirst($data->relastname);
            $data->dti_datereq = Date::parse($data->dti_datereq)->toFormattedDateString();
            $data->dti_dateneed = Date::parse($data->dti_dateneed)->toFormattedDateString();
            $data->dti_approveddate = Date::parse($data->dti_approveddate)->toFormattedDateString();
            $data->timerequested = Date::parse($data->dti_approveddate)->format('H:i');
        }
        return $data;
    }


    public function getDataListDti($id)
    {
        $data = DtiBarcodes::select(
            'dti_denom',
            'fname',
            'lname',
            'mname',
            'extname',
            'dti_barcode',
        )->where([
            ['dti_trid', $id],
            ['dti_status', '']
        ]);

        return response()->json([
            'record' => $data->get(),
            'total' => $data->count(),
            'denomcount' => $data->sum('dti_denom'),
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

    public function submitPayementFormDti($request)
    {
        $request->validate([
            'paymentstats' => 'required',
            'paymentType' => 'required',
            'checkedby' => 'required',
            'recby' => 'required',
            // 'status' => 'required',
            'remarks' => 'required',
        ]);

        if ($request->paymentType === '1') {
            $request->validate([
                'amount' => 'required',
            ]);
        }
        if ($request->paymentType === '2') {
            $request->validate([
                'checkamount' => 'required',
                'checkno' => 'required',
                'bankname' => 'required',
                'accountno' => 'required',
            ]);
        }
        if ($request->payement === '3') {
            $request->validate([
                'custname' => 'required',
            ]);
        }

        if (empty($request->checked)) {
            return back()->with([
                'status' => 'error',
                'msg' => 'Please Select Atleast One Record First!',
                'title' => 'Opps Error',
            ]);
        }
        if ($this->ifExists($request)) {

            $relid = $this->getSpecialGCReleasingNoDti();

            DB::transaction(function () use ($request, $relid) {
                $this->dbservices->insertIntoDtiApprovedRequest($request, $relid)
                ->insertIntoLedgerBudgetDtiNew($request)
                ->updateDtiGcRequest($request)
                ->insertInstitutionalDtiPayment($request)
                ->updateDtiBarcodes($request);
            });

        }else{
            return back()->with([
                'status' => 'error',
                'msg' => 'Opss something went Wrong!!',
                'title' => 'Error',
            ]);
        }
    }
    private function ifExists($request)
    {
        return DtiGcRequest::where('dti_payment_stat', '!=', 'FINAL')
            ->where('dti_num', $request->id)
            ->exists();
    }
    private function getSpecialGCReleasingNoDti()
    {
        $data = DtiApprovedRequest::where('dti_approvedtype', 'special external releasing')->orderByDesc('dti_trnum')->max('dti_trnum');

        if ($data > 0) {
            return $data++;
        }

        return 1;
    }

}
