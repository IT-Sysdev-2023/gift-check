<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductionRequest extends Model
{
    use HasFactory;

    protected $table= 'production_request';

    protected $primaryKey= 'pe_id';

    protected function casts(): array
    {
        return [
            'pe_date_request' => 'datetime',
            'pe_date_needed' => 'date'
        ];
    }

    public function user(){
        return $this->belongsTo(User::class, 'pe_requested_by', 'user_id');
    }

    public function approvedProductionRequest(): BelongsTo
    {
        return $this->belongsTo(ApprovedProductionRequest::class, 'pe_id', 'ape_pro_request_id');
    }
}
