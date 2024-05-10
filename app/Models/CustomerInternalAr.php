<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerInternalAr extends Model
{
    use HasFactory;

    protected $table = 'customer_internal_ar';
    protected $primaryKey = 'ar_id';
}
