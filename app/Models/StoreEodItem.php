<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreEodItem extends Model
{
    use HasFactory;

    protected $primaryKey = 'st_eod_id';


    protected $guarded = [];

    public $timestamps = false;

    public function storeverification(){
        return $this->belongsTo(StoreVerification::class, 'st_eod_barcode', 'vs_barcode');
    }

    // public function customers(){
    //     // return $this->belongsTo(Customer)
    // }
}
