<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BudgetRequest extends Model
{
    use HasFactory;

    protected $table = 'budget_request';
    protected $primaryKey = 'br_id';
    protected $guarded = [];
    public $timestamps = false;
    
    public function scopeFilter(Builder $builder, $filter)
    {
        return $builder->when($filter['date'] ?? null, function ($query, $date) {
            $query->whereBetween('br_requested_at', [$date[0], $date[1]]);
        })->when($filter['search'] ?? null, function ($query, $search) {
            $query->whereAny([
                'br_request',
                'br_no',
                'br_requested_at',
            ], 'LIKE', '%' . $search . '%')
            ->orWhereHas('approvedBudgetRequest', function (Builder $query) use ($search) {
                $query->whereAny([
                    'abr_approved_by',
                    'abr_approved_at'
                ], 'LIKE', '%' . $search . '%');
            })
            ->orWhereHas('user', function (Builder $query) use ($search) {
                $query->whereAny([
                    'firstname',
                    'lastname'
                ], 'LIKE', '%' . $search . '%');
            });
        });
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'br_requested_by', 'user_id');
    }
    public function cancelledBudgetRequest(): BelongsTo
    {
        return $this->belongsTo(CancelledBudgetRequest::class, 'br_id', 'cdreq_req_id');
    }
    public function approvedBudgetRequest()
    {
        return $this->belongsTo(ApprovedBudgetRequest::class, 'br_id', 'abr_budget_request_id');
    }
}
