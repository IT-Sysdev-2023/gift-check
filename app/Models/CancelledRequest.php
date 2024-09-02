<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CancelledRequest extends Model
{
    use HasFactory;

    protected $table = 'cancelled_request';
    protected $primaryKey = 'reqcan_id';

    protected $guarded = [];

    public $timestamps = false;
}
