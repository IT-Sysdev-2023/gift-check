<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionRequestItem extends Model
{
    use HasFactory;

    protected $primaryKey = 'pe_items_id';
    protected $guarded = [];
    public $timestamps = false;


    public function scopeSelectFilter($query)
    {
        $query->select(
            'denomination',
            'pe_items_quantity',
            'pe_items_request_id',
            'pe_items_denomination'
        );
    }

    public function denomination()
    {
        return $this->belongsTo(Denomination::class, 'pe_items_denomination', 'denom_id');
    }

    public function barcodeStartEnd()
    {
        return $this->hasMany(Gc::class,'pe_entry_gc','pe_items_request_id');
    }

}
