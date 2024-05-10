<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentFund extends Model
{
    use HasFactory;

    protected $table= 'payment_fund';

    protected $primaryKey= 'pay_id';
}
