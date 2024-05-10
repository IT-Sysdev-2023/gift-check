<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreReceived extends Model
{
    use HasFactory;
    protected $table= 'store_received';

    protected $primaryKey= 'srec_id';
}
