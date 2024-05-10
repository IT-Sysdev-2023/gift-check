<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovedBudgetRequest extends Model
{
    use HasFactory;

    protected $table = 'approved_budget_request';
    protected $primaryKey = 'abr_id';
}
