<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LedgerStore extends Model
{
    use HasFactory;

    protected $table = 'ledger_store';
    protected $primaryKey = 'sledger_id';
    
}
