<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditcardPayment extends Model
{
    use HasFactory;
    protected $table = 'creditcard_payment';
    protected $primaryKey = 'ccpayment_id';
}
