<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gc extends Model
{
    use HasFactory;

    protected $table = 'gc';
    protected $primaryKey = 'gc_id';
    public $timestamps = false;
    protected $guarded = [];

    public function denomination(){
        return $this->belongsTo(Denomination::class, 'denom_id', 'denom_id');
    }

    public function productionRequest(){
        return $this->belongsTo(ProductionRequest::class, 'pe_entry_gc', 'pe_id');
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
    public function barcodeInst(){
        return $this->belongsTo(InstitutTransactionsItem::class, 'barcode_no', 'instituttritems_barcode');
    }
    public function custodianSrrItems(){
        return $this->hasOne(CustodianSrrItem::class, 'cssitem_barcode', 'barcode_no');
    }
    public function tempreceived(){
        return $this->hasMany(TempReceivestore::class, 'trec_denid', 'denom_id');
    }
    public function promoGcReleasedToItems(){
        return $this->belongsTo(PromoGcReleaseToItem::class, 'barcode_no', 'prreltoi_barcode');
    }
    public function gcLocation(){
        return $this->belongsTo(GcLocation::class, 'barcode_no', 'loc_barcode_no');
    }
    public function institutTransactionsItem(){
        return $this->belongsTo(InstitutTransactionsItem::class, 'barcode_no', 'instituttritems_barcode');
    }
}
