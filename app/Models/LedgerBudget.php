<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LedgerBudget extends Model
{
    use HasFactory;

    protected $table = 'ledger_budget';
    protected $primaryKey = 'bledger_id';
}
