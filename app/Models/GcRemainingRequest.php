<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GcRemainingRequest extends Model
{
    use HasFactory;

    protected $table = 'gc_remaining_request';
    protected $primaryKey = 'gc_remainreq_id';
}
