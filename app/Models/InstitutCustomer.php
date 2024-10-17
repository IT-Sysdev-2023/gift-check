<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InstitutCustomer extends Model
{
    use HasFactory;
    const CREATED_AT = 'ins_date_created';
    const UPDATED_AT = 'ins_date_updated';
    protected $guarded=[];
    protected $table = 'institut_customer';
    protected $primaryKey = 'ins_id';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ins_by','user_id');
    }
}
