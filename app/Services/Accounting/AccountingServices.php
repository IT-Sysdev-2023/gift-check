<?php

namespace App\Services\Accounting;

use App\Models\SpecialExternalGcrequest;
use App\Models\SpecialExternalGcrequestEmpAssign;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Str;

class AccountingServices
{
    public function getPaymentList()
    {
        $data = SpecialExternalGcrequest::select(
            'spexgc_id',
            'spexgc_num',
            'spexgc_datereq',
            'spexgc_dateneed',
            'spcus_acctname',
            'spcus_companyname',
            'spexgc_company',
            'spcus_id',
            'spexgc_balance',
            'spexgc_payment_stat',
            'reqap_date',
            'reqap_approvedtype',
            'spexgc_reviewed',
            'spexgc_released',
            'spexgc_payment_stat',
            'spexgc_reqby',
            'reqby.firstname',
            'reqby.lastname',
        )->join('special_external_customer', 'spcus_id', '=', 'spexgc_company')
            ->join('approved_request', 'reqap_trid', '=', 'spexgc_id')
            ->join('users as reqby', 'user_id', '=', 'spexgc_reqby')
            ->where('spexgc_released', 'released')
            ->orWhere('spexgc_reviewed', 'reviewed')
            ->where('reqap_approvedtype', 'special external releasing')
            ->orWhere('reqap_approvedtype', 'special external gc review')
            ->where('spexgc_payment_stat', '!=', 'FINAL')
            ->where('spexgc_payment_stat', '!=', 'WHOLE')
            ->orderByDesc('spexgc_num')
            ->paginate(10)
            ->withQueryString();

        $data->transform(function ($item) {

            $item->reqby = Str::ucfirst($item->firstname) . ' ' . Str::ucfirst($item->lastname);

            return $item;
        });


        return $data;
    }

    public function getDetialsEveryPayment($id)
    {

        $data = SpecialExternalGcrequest::select(
            'spexgc_id',
            'spexgc_num',
            'spexgc_datereq',
            'spexgc_dateneed',
            'spcus_acctname',
            'spcus_companyname',
            'spexgc_company',
            'spcus_id',
            'spexgc_balance',
            'spexgc_payment_stat',
            'reqap_date',
            'reqap_remarks',
            'reqap_approvedtype',
            'spexgc_reviewed',
            'spexgc_released',
            'spexgc_payment_stat',
            'spexgc_payment',
            'spexgc_remarks',
            'spexgc_reqby',
            'reqby.firstname as fn',
            'reqby.lastname as ln',
            'prepby.firstname',
            'prepby.lastname',
            'title',
            'reqap_checkedby',
            'reqap_approvedby',
        )->join('special_external_customer', 'spcus_id', '=', 'spexgc_company')
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
}
