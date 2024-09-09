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
}
