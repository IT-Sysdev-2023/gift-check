<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BudgetAdjustment extends Model
{
    use HasFactory;

    protected $table = 'budget_adjustment';
    protected $primaryKey = 'bud_id';
}
