<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionPayment extends Model
{
    use HasFactory;

    protected $table= 'transaction_payment';

    protected $primaryKey= 'payment_id';

    public function transactionStore(){
        return $this->belongsTo(TransactionStore::class, 'payment_trans_num', 'trans_sid');
    }
}
