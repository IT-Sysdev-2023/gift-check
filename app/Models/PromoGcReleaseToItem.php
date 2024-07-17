<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromoGcReleaseToItem extends Model
{
    use HasFactory;

    protected $primaryKey = 'prreltoi_id';


    public function iadBarcode()
    {
        return $this->belongsTo(CustodianSrrItem::class, 'prreltoi_barcode', 'cssitem_barcode');
    }
}
