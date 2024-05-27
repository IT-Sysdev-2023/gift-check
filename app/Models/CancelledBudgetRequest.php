<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CancelledBudgetRequest extends Model
{
    use HasFactory;

    protected $table = 'cancelled_budget_request';
    protected $primaryKey = 'cdreq_id';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cdreq_by', 'user_id');
    }
}
