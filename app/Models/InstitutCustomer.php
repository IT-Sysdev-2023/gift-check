<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InstitutCustomer extends Model
{
    use HasFactory;

    protected $table = 'institut_customer';
    protected $primaryKey = 'ins_id';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ins_by','user_id');
    }
}
