<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CancelledBudgetRequest extends Model
{
    use HasFactory;

    protected $table = 'cancelled_budget_request';
    protected $primaryKey = 'cdreq_id';
}
