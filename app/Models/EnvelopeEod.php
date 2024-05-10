<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnvelopeEod extends Model
{
    use HasFactory;

    protected $table = 'envelope_eod';
    protected $primaryKey = 'env_eod_id';
}
