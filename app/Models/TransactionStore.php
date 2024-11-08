<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionStore extends Model
{
    use HasFactory;

    protected $primaryKey ='trans_sid';

    protected function casts(): array {
        return [
            'trans_datetime' => 'datetime'
        ];
    }

    public function ledgerStore(){
        return $this->hasOne(LedgerStore::class, 'sledger_ref', 'trans_sid');
    }
}
