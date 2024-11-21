<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LostGcDetail extends Model
{
    use HasFactory;

    protected $primaryKey = 'lostgcd_id';
    protected $guarded = [];
    public $timestamps = false;
}
