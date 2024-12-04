<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BudgetAdjustment extends Model
{
    use HasFactory;

    protected $table = 'budgetadjustment';
    protected $primaryKey = 'bud_id';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'adj_requested_by', 'user_id');
    }
}
