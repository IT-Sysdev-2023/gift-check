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

}
