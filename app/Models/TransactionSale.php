<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionSale extends Model
{
    use HasFactory;
    protected $primaryKey= 'sales_id';

    public function transactionStore(){
        return $this->hasMany(TransactionStore::class, 'trans_sid', 'sales_transaction_id');
    }

    public function denomination(){
        return $this->belongsTo(Denomination::class, 'sales_denomination','denom_id');
    }

}
