<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionRevalidation extends Model
{
    use HasFactory;

    protected $table= 'transaction_revalidation';

    protected $primaryKey= 'reval_id';

    protected $guarded = [];

    public $timestamps = false;

    public function trans_stores(){
        $this->belongsTo(TransactionStore::class,'trans_sid','reval_trans_id');
    }
}
