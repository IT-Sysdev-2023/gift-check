<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionRefundDetail extends Model
{
    use HasFactory;

    protected $primaryKey = 'trefundd_id';

    public function transactionStore(){
        return $this->belongsTo(TransactionStore::class, 'trefundd_trstoresid','trans_sid');
    }
}
