<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LostGcBarcode extends Model
{
    use HasFactory;

    protected $primaryKey = 'lostgcb_id';

    protected $guarded = [];
    public $timestamps = false;
}
