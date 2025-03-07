<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DtiInstitutPayment extends Model
{
    //
    protected $guarded = [];

    public function scopeSelectFilterInst($query){
        $query->select(
            'dti_insp_id',
            'dti_insp_paymentnum',
            'dti_insp_trid',
            'dti_insp_paymentcustomer',
            'dti_institut_amountrec',
            'dti_institut_date',
            'dti_num',
            'dti_payment_stat',
            'spcus_companyname',
            'spcus_acctname',
        );
    }
}
