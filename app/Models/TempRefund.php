<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempRefund extends Model
{
    use HasFactory;

    protected $table= 'temp_refund';

    protected $primaryKey= 'trfund_id';
}
