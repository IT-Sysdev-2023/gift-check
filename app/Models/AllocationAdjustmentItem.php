<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class AllocationAdjustmentItem extends Model
{
    use HasFactory;
    protected $primaryKey = 'addji_id';

    protected $guarded = [];

    public $timestamps = false;

    public function scopeFilterDenomination(Builder $builder, $filter)
    {
        return $builder->when($filter['search'] ?? null, function ($query, $search) {
            $query->whereHas('gc.denomination', function (Builder $query) use ($search) {
                $query->whereAny([
                    'denomination',
                ], 'LIKE', '%' . $search . '%');
            });
        });
    }

    public function gc(){
        return $this->belongsTo(Gc::class, 'aadji_barcode', 'barcode_no');
    }
}
