<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreReceivedGc extends Model
{
    use HasFactory;

    protected $table= 'store_received_gc';

    protected $primaryKey= 'strec_id';
}
