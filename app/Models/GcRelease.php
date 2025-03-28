<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GcRelease extends Model
{
    use HasFactory;

    protected $table = 'gc_release';
    protected $primaryKey = 'rel_id';

    protected $guarded = [];

    public $timestamps = false;

    public function gc()
    {
        return $this->belongsTo(Gc::class, 're_barcode_no', 'barcode_no');
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'rel_store_id', 'store_id');
    }
    public function scopeJoinGcDenomination(Builder $builder){
        return $builder->join('gc', 'gc.barcode_no', '=', 'gc_release.re_barcode_no')
        ->join('denomination', 'denomination.denom_id', '=', 'gc.denom_id');
    }

}
