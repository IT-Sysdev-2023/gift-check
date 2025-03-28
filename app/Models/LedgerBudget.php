<?php

namespace App\Models;

use App\Helpers\NumberHelper;
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

    protected $guarded = [];
    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'bledger_datetime' => 'date'
        ];
    }

    public function scopeFilter($builder, $filter)
    {
        return $builder->when($filter['date'] ?? null, function ($query, $date) {
            $query->whereBetween('bledger_datetime', [$date[0], $date[1]]);
        })->when($filter['search'] ?? null, function ($query, $search) {
            $query->whereAny([
                'bledger_no',
                'bledger_type',
                'bledger_datetime',
                'bdebit_amt',
                'bcredit_amt',
            ], 'LIKE', '%' . $search . '%');
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

    public function budgetAdjustment(): BelongsTo
    {
        return $this->belongsTo(BudgetAdjustment::class, 'bledger_trid', 'bud_id');
    }

    public function approvedGcRequest()
    {
        return $this->belongsTo(ApprovedGcrequest::class, 'bledger_trid', 'agcr_id');
    }

    public static function budget()
    {
        $query = self::select(DB::raw('SUM(bdebit_amt) as debit'), DB::raw('SUM(bcredit_amt) as credit'))
            ->whereNot('bcus_guide', 'dti')
            ->whereNull('bledger_category')
            ->first();

        return bcsub($query->debit, $query->credit, 2);
    }
    public static function budgetRegular()
    {
        $query = self::select(DB::raw('SUM(bdebit_amt) as debit'), DB::raw('SUM(bcredit_amt) as credit'))
            ->whereNot('bcus_guide', 'dti')
            ->where('bledger_category', 'regular')
            ->first();

        return bcsub($query->debit, $query->credit, 2);
    }
    public static function budgetSpecial()
    {
        $query = self::select(DB::raw('SUM(bdebit_amt) as debit'), DB::raw('SUM(bcredit_amt) as credit'))
            ->whereNot('bcus_guide', 'dti')
            ->where('bledger_category', 'special')
            ->first();

        return bcsub($query->debit, $query->credit, 2);
    }
    public static function currentBudget()
    {
        return NumberHelper::currency((float) self::budget());
    }

    public static function specialBudget()
    {
        $query = self::select(DB::raw('SUM(bdebit_amt) as debit'), DB::raw('SUM(bcredit_amt) as credit'))
            ->where('bledger_category', 'special')
            ->whereNot('bcus_guide', 'dti')->first();

        return NumberHelper::currency((float) bcsub($query->debit, $query->credit, 2));
    }
    public static function regularBudget()
    {
        $query = self::select(DB::raw('SUM(bdebit_amt) as debit'), DB::raw('SUM(bcredit_amt) as credit'))
            ->where('bledger_category', 'regular')
            ->whereNot('bcus_guide', 'dti')->first();

        return NumberHelper::currency( (float) bcsub($query->debit, $query->credit, 2));
    }
}
