<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LedgerCreditcard extends Model
{
    use HasFactory;

    protected $table= 'ledger_creditcard';

    protected $primaryKey = 'ccled_id';
}
