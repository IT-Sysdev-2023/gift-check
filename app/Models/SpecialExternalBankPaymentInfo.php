<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpecialExternalBankPaymentInfo extends Model
{
    use HasFactory;

    protected $table= 'special_external_bank_payment_info';

    protected $primaryKey= 'spexgcbi_trid';
    public $timestamps = false;
    protected $guarded = [];
}
