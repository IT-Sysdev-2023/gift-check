<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstitutTransaction extends Model
{
    use HasFactory;

    protected $primaryKey = 'institutr_id';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'institutr_date' => 'datetime'
        ];
    }


    public $timestamps = false;
    public function institutPayment(){
        return $this->belongsTo(InstitutPayment::class, 'institutr_id', 'insp_trid');
    }
    public function institutCustomer(){
        return $this->belongsTo(InstitutCustomer::class, 'institutr_cusid', 'ins_id');
    }

    public function institutTransactionItem(){
        return $this->hasMany(InstitutTransactionsItem::class, 'instituttritems_trid', 'institutr_id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'institutr_trby', 'user_id');
    }

    public function document()
    {
        return $this->belongsTo(Document::class, 'institutr_id', 'doc_trid');
    }

    // public function gc()
    // {
    //     return $this->hasMany(Gc::class, 'barcode_no', 'instituttritems_barcode');
    // }
}
