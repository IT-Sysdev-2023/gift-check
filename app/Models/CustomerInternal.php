<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerInternal extends Model
{
    use HasFactory;

    protected $table = 'customer_internal';
    protected $primaryKey = 'ci_code';
}
