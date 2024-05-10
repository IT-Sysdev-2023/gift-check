<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnvelopeProductionReq extends Model
{
    use HasFactory;

    protected $table = 'envelope_production_req';
    protected $primaryKey = 'env_pe_id';
}
