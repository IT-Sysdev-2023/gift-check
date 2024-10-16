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

    protected function casts(): array
    {
        return [
            'cpr_at' => 'datetime'
        ];
    }
    public function user(): BelongsTo{
        return $this->belongsTo(User::class, 'cpr_by', 'user_id');
    }

    public function productionRequest(){
        return $this->belongsTo(ProductionRequest::class, 'cpr_pro_id','pe_id');
    }
}
