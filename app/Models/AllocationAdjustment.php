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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'aadj_by', 'user_id');
    }
}
