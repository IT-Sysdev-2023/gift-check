<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Suppliergc extends Model
{
    use HasFactory;

    protected $table = 'suppliergc';
    protected $guarded = [];
    protected $primaryKey = 'suppgc_id';
    public $timestamps = false;
}
