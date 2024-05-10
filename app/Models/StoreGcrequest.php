<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreGcrequest extends Model
{
    use HasFactory;

    protected $table= 'store_gcrequest';

    protected $primaryKey= 'sgc_id';
}
