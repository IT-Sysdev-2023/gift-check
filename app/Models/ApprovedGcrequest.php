<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovedGcrequest extends Model
{
    use HasFactory;

    protected $table = 'approved_gcrequest';
    protected $primaryKey = 'agcr_id';
}
