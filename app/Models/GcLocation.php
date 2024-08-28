<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class GcLocation extends Model
{
    use HasFactory;

    protected $table = 'gc_location';
    protected $primaryKey = 'loc_id';
    protected $guarded = [];
    public $timestamps = false;

    public function scopeFilter(Builder $builder, $filter)
    {
        return $builder->when($filter['search'] ?? null, function ($query, $search) {
            $query->whereAny([
                'loc_barcode_no',
            ], 'LIKE', '%' . $search . '%')
            ->orWhereHas('gc', function (Builder $query) use ($search) {
                $query->whereAny([
                    'pe_entry_gc',
                ], 'LIKE', '%' . $search . '%');
            })
            ->orWhereHas('gc.denomination', function (Builder $query) use ($search) {
                $query->whereAny([
                    'denomination',
                ], 'LIKE', '%' . $search . '%');
            });
        });
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'loc_by', 'user_id');
    }

    public function gcType()
    {
        return $this->belongsTo(GcType::class, 'loc_gc_type', 'gc_type_id');
    }
    public function gc()
    {
        return $this->belongsTo(Gc::class, 'loc_barcode_no', 'barcode_no');
    }

}
