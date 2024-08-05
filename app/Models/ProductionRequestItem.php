<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionRequestItem extends Model
{
    use HasFactory;

    protected $primaryKey = 'pe_items_id';


    public function scopeSelectFilter($query){
        $query->select(
            'denomination',
            'pe_items_quantity',
            'pe_items_request_id',
            'pe_items_denomination'
        );
    }
}
