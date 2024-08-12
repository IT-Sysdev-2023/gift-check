<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CancelledProductionRequest extends Model
{
    use HasFactory;

    protected $table = 'cancelled_production_request';
    protected $primaryKey = 'cpr_id';

    protected $guarded=[];
    public $timestamps= false;

    public function user(): BelongsTo{
        return $this->belongsTo(User::class, 'pe_requested_by', 'user_id');
    }
}
