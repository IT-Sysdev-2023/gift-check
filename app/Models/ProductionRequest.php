<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class ProductionRequest extends Model
{
    use HasFactory;

    protected $table = 'production_request';
    protected $guarded = [];
    protected $primaryKey = 'pe_id';
    public $timestamps = false;

    public $timestamps= false;

    protected function casts(): array
    {
        return [
            'pe_date_request' => 'datetime',
            'pe_date_needed' => 'date'
        ];
    }

    public function scopeFilter(Builder $builder, $filter)
    {
        return $builder->when($filter['date'] ?? null, function ($query, $date) {
            $query->whereBetween('pe_date_request', [$date[0], $date[1]]);
        })->when($filter['search'] ?? null, function ($query, $search) {
            $query->whereAny([
                'pe_requested_by',
                'pe_num',
            ], 'LIKE', '%' . $search . '%')
                ->orWhereHas('approvedProductionRequest', function (Builder $query) use ($search) {
                    $query->where('ape_approved_by', 'LIKE', '%' . $search . '%');
                });
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'pe_requested_by', 'user_id');
    }

    public function approvedProductionRequest(): BelongsTo
    {
        return $this->belongsTo(ApprovedProductionRequest::class, 'pe_id', 'ape_pro_request_id');
    }
}
