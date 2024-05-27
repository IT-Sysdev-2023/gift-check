<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InstitutEod extends Model
{
    use HasFactory;

    protected $table= 'institut_eod';
    protected $primaryKey = 'ieod_id';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ieod_by', 'user_id');
    }
}
