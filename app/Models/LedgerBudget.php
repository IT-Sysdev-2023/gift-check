<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use DateTimeInterface;

class LedgerBudget extends Model
{
    use HasFactory;

    protected $table = 'ledger_budget';
    protected $primaryKey = 'bledger_id';

    protected function casts(): array
    {
        return [
            'bledger_datetime' => 'date'
        ];
    }

    public function scopeFilter($builder, $filter){
        return $builder->when($filter ?? null, function($query, $filt){
            $query->whereBetween('bledger_datetime',[$filt[0], $filt[1]]);
        });
    }
    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->toFormattedDateString();
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'bud_by', 'user_id');
    }

    public function budgetAdjustment(): BelongsTo{
        return $this->belongsTo(BudgetAdjustment::class, 'bledger_trid', 'bud_id');
    }

    public static function currentBudget()
    {
        $query = self::select(DB::raw('SUM(bdebit_amt) as debit'), DB::raw('SUM(bcredit_amt) as credit'))
            ->whereNot('bcus_guide', 'dti')->first();
        return bcsub($query->debit, $query->credit, 2);
    }
}
