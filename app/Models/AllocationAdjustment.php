<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AllocationAdjustment extends Model
{
    use HasFactory;

    protected $table = 'allocation_adjustment';
    protected $primaryKey = 'aadj_id';
}
