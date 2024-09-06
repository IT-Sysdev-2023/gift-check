<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreEodItem extends Model
{
    use HasFactory;

    protected $primaryKey = 'st_eod_id';


    protected $guarded = [];

    public $timestamps = false;
}
