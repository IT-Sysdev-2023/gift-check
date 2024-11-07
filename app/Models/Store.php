<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Store extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $primaryKey = 'store_id';
    public $timestamps = false;

    public function scopeSelectStore(Builder $query){
        $query->select('store_id as value', 'store_name as label')->where('store_status', 'active');
    }
}
