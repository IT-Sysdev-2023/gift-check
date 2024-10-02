<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstitutPayment extends Model
{
    use HasFactory;

    protected $table = 'institut_payment';
    protected $primaryKey = 'insp_id';

    protected $guarded =[];
    public $timestamps = false;

    public static function paymentNumber(){
        $ip = InstitutPayment::max('insp_paymentnum');
        return $ip ? $ip + 1 : 1;
    }
    public function scopeSelectFilterInst($query){
        $query->select(
            'insp_id',
            'insp_paymentnum',
            'insp_trid',
            'insp_paymentcustomer',
            'institut_amountrec',
            'institut_date',
            'spexgc_num',
            'spexgc_payment_stat',
            'spcus_companyname',
            'spcus_acctname',
        );
    }
}
