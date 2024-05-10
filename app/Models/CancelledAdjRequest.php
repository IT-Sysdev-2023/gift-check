<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CancelledAdjRequest extends Model
{
    use HasFactory;

    protected $table = 'cancelled_adj_request';
    protected $primaryKey = 'cadj_id';
}
