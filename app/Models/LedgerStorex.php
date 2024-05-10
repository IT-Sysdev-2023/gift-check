<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LedgerStorex extends Model
{
    use HasFactory;

    protected $table = 'ledger_storex';
    protected $primaryKey = 'sledger_id';
}
