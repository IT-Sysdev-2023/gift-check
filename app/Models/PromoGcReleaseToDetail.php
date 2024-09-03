<?php

namespace App\Models;

use App\Helpers\NumberHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PromoGcReleaseToDetail extends Model
{
    use HasFactory;

    protected $primaryKey = 'prrelto_id';
    protected $guarded = [];
    public $timestamps = false;

    public static function getMax(){
        $max = self::max('prrelto_relnumber');
        $inc = $max ? $max + 1 : 1;
        return NumberHelper::leadingZero($inc);
    } 
    public function user(): BelongsTo{
        return $this->belongsTo(User::class, 'prrelto_relby', 'user_id');
    }

    public function promoGcRequest(): BelongsTo
    {
        return $this->belongsTo(PromoGcRequest::class, 'prrelto_trid', 'pgcreq_id');
    }
}
