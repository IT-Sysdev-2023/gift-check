<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LedgerBudget extends Model
{
    use HasFactory;

    protected $table = 'ledger_budget';
    protected $primaryKey = 'bledger_id';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'bud_by', 'user_id');
    }

    public function budgetAdjustment(): BelongsTo{
        return $this->belongsTo(BudgetAdjustment::class, 'bledger_trid', 'bud_id');
    }
}
