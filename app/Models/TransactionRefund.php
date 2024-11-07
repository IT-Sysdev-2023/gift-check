<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionRefund extends Model
{
    use HasFactory;

    protected $table= 'transaction_refund';

    protected $primaryKey= 'refund_id';

    public function transactionStore(){
        return $this->belongsTo(TransactionStore::class, 'refund_trans_id', 'trans_sid');
    }

    public function denomination(){
        return $this->belongsTo(Denomination::class, 'refund_denom', 'denom_id');
    }
}
