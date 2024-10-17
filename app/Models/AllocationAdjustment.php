<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AllocationAdjustment extends Model
{
    use HasFactory;

    protected $table = 'allocation_adjustment';
    protected $primaryKey = 'aadj_id';

    protected function casts(): array
    {
        return [
            'aadj_datetime' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'aadj_by', 'user_id');
    }

    // ->join('stores', 'allocation_adjustment.aadj_loc', '=', 'stores.store_id')
            // ->join('gc_type', 'allocation_adjustment.aadj_gctype', '=', 'gc_type.gc_type_id')
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'aadj_loc', 'store_id');
    }

    public function gcType(): BelongsTo
    {
        return $this->belongsTo(GcType::class, 'aadj_gctype', 'gc_type_id');
    }
}
