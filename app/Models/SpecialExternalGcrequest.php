<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpecialExternalGcrequest extends Model
{
    use HasFactory;

    protected $table= 'special_external_gcrequest';

    protected $primaryKey= 'spexgc_id';
}
