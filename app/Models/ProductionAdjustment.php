<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionAdjustment extends Model
{
    use HasFactory;

    protected $table= 'production_adjustment';

    protected $primaryKey= 'proadj_id';
}
