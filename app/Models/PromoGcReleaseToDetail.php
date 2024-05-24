<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PromoGcReleaseToDetail extends Model
{
    use HasFactory;

    protected $primaryKey = 'prrelto_id';


    public function user(): BelongsTo{
        return $this->belongsTo(User::class, 'prrelto_relby', 'user_id');
    }

    public function promoGcRequest(): BelongsTo
    {
        return $this->belongsTo(PromoGcRequest::class, 'prrelto_trid', 'pgcreq_id');
    }
}
