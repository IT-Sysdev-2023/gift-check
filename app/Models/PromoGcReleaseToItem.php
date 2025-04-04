<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromoGcReleaseToItem extends Model
{
    use HasFactory;

    protected $primaryKey = 'prreltoi_id';

    protected $guarded = [];
    public $timestamps = false;
    public function iadBarcode()
    {
        return $this->belongsTo(CustodianSrrItem::class, 'prreltoi_barcode', 'cssitem_barcode');
    }
    public function treasuryCfsBarcode(){
        return $this->belongsTo(StoreReceivedGc::class, 'prreltoi_barcode', 'strec_barcode');
    }
    public function reverified(){
        return $this->belongsTo(StoreVerification::class, 'prreltoi_barcode', 'vs_barcode');
    }
    public function gc(){
        return $this->belongsTo(Gc::class, 'prreltoi_barcode', 'barcode_no');
    }
}
