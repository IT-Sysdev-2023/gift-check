<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LedgerSpgc extends Model
{
    use HasFactory;

    protected $table = 'ledger_spgc';
    protected $primaryKey = 'spgcledger_id';

    protected function casts(): array
    {
        return [
            'spgcledger_datetime' => 'date'
        ];
    }


    public function scopeFilter($builder, $filter)
    {
        return $builder->when($filter['date'] ?? null, function ($query, $date) {
            $query->whereBetween('spgcledger_datetime', [$date[0], $date[1]]);
        })
            ->when($filter['search'] ?? null, function ($query, $search) {
                $query->whereAny([
                    'spgcledger_no',
                    'spgcledger_type',
                    'spgcledger_credit',
                    'spgcledger_trid',
                ], 'LIKE', '%' . $search . '%');
            });
    }
}
