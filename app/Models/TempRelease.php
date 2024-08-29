<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempRelease extends Model
{
    use HasFactory;
    protected $table = 'temp_release';

    protected $primaryKey = 'temp_rbarcode';

    protected $guarded = [];
    public $timestamps = false;
}
