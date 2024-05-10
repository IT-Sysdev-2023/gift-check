<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CancelledProductionRequest extends Model
{
    use HasFactory;

    protected $table = 'cancelled_production_request';
    protected $primaryKey = 'cpr_id';
}
