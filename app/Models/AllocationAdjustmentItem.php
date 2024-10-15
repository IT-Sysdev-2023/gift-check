<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AllocationAdjustmentItem extends Model
{
    use HasFactory;
    protected $primaryKey = 'addji_id';

    public function gc(){
        return $this->belongsTo(Gc::class, 'aadji_barcode', 'barcode_no');
    }
}
