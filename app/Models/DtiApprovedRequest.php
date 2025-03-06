<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DtiApprovedRequest extends Model
{
    protected $guarded = [];
    public function user(): BelongsTo {
        return $this->belongsTo(User::class, 'dti_preparedby', 'user_id');
    }
}
