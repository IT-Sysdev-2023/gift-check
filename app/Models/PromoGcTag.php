<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromoGcTag extends Model
{
    use HasFactory;

    protected $table= 'promo_gc_tag';

    protected $primaryKey= 'promotagId';
}
