<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GcRelease extends Model
{
    use HasFactory;

    protected $table = 'gc_release';
    protected $primaryKey = 'rel_id';
}
