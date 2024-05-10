<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GcAdjustment extends Model
{
    use HasFactory;

    protected $table = 'gcadjustment';
    protected $primaryKey = 'gc_adj_id';
}
