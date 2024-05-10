<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManualSetgc extends Model
{
    use HasFactory;

    protected $table = 'manual_setgc';
    protected $primaryKey = 'mgc_id';
}
