<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CancelledStoreGcrequest extends Model
{
    use HasFactory;

    protected $table = 'cancelled_store_gcrequest';
    protected $primaryKey = 'csgr_id';
}
