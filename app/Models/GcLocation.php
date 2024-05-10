<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GcLocation extends Model
{
    use HasFactory;

    protected $table = 'gc_location';
    protected $primaryKey = 'loc_id';
}
