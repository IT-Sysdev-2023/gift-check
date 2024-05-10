<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovedRequest extends Model
{
    use HasFactory;

    protected $table = 'approved_request';
    protected $primaryKey = 'reqap_id';
}
