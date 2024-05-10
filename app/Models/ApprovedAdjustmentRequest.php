<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovedAdjustmentRequest extends Model
{
    use HasFactory;

    protected $table = 'approved_adjustment_request';
    protected $primaryKey = 'app_adjid';
}
