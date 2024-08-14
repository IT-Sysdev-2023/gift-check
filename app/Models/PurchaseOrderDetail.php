<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderDetail extends Model
{
    use HasFactory;

    protected $table = 'purchase_orderdetails';

    protected $primaryKey = 'purchorderdet_ref';

    protected $guarded = [];

    public $timestamps = false;
}
