<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LedgerCheck extends Model
{
    use HasFactory;

    protected $table= 'ledger_check';

    protected $primaryKey = 'cledger_id';
}
