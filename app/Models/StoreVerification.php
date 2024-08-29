<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreVerification extends Model
{
    use HasFactory;

    protected $table = 'store_verification';

    protected $primaryKey = 'vs_id';

    protected $guarded = [];

    public $timestamps = false;
}
