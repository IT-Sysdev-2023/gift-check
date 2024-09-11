<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstitutTransactionsItem extends Model
{
    use HasFactory;

    protected $primaryKey = 'instituttritems_id';
    protected $guarded = [];
    public $timestamps = false;
    public function reverified(){
        return $this->belongsTo(StoreVerification::class, 'instituttritems_barcode', 'vs_barcode');
    }
}
