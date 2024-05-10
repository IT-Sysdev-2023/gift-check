<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GcType extends Model
{
    use HasFactory;

    protected $table= 'gc_type';
    protected $primaryKey = 'gc_type_id';
    
}
