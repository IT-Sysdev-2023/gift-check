<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GcReturn extends Model
{
    use HasFactory;

    protected $table = 'gc_return';
    protected $primaryKey = 'rr_id';
}
