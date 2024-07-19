<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gc extends Model
{
    use HasFactory;

    protected $table = 'gc';
    protected $primaryKey = 'gc_id';

    public function denomination(){
        return $this->belongsTo(Denomination::class, 'denom_id', 'denom_id');
    }

    public function barcode(){
        return $this->belongsTo(BarcodeChecker::class, 'barcode_no', 'bcheck_barcode');
    }
    public function iadBarcode(){
        return $this->belongsTo(CustodianSrrItem::class, 'barcode_no', 'cssitem_barcode');
    }
    public function treasuryCfsBarcode(){
        return $this->belongsTo(StoreReceivedGc::class, 'barcode_no', 'strec_barcode');
    }
    public function reverified(){
        return $this->belongsTo(StoreVerification::class, 'barcode_no', 'vs_barcode');
    }
    public function barcodePromo(){
        return $this->belongsTo(PromoGcReleaseToItem::class, 'barcode_no', 'prreltoi_barcode');
    }
    public function custodianSrrItems(){
        return $this->hasOne(CustodianSrrItem::class, 'cssitem_barcode', 'barcode_no');
    }
}
