<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreEodTextfileTransaction extends Model
{
    use HasFactory;

    protected $primaryKey = 'seodtt_id';


    protected $guarded = [];

    public $timestamps = false;
}
