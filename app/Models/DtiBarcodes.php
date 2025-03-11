<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DtiBarcodes extends Model
{
    protected $table = 'dti_barcodes';
    protected $guarded = [];
    public $timestamps = false;

    public function dtigcrequest(){
        return $this->belongsTo(DtiGcRequest::class, 'dti_trid', 'dti_num');
    }
    public function reverified()
    {
        return $this->belongsTo(StoreVerification::class, 'dti_barcode', 'vs_barcode');
    }

}
