<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromoGcRequest extends Model
{
    use HasFactory;

    protected $table= 'promo_gc_request';

    protected $primaryKey= 'pgcreq_id';

    public $timestamps = false;


    protected $guarded = [];
}
