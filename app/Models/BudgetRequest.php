<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BudgetRequest extends Model
{
    use HasFactory;

    protected $table = 'budget_request';
    protected $primaryKey = 'br_id';
    protected $guarded = [];
    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'br_requested_at' => 'datetime',
            'br_requested_needed' => 'datetime',
        ];
    }

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
    public function scopeSelectFilter($query)
    {
        $query->select(
            'br_id',
            'br_request',
            'br_requested_at',
            'br_requested_by',
            'br_no',
            'br_file_docno',
            'br_remarks',
            'br_requested_needed',
            'firstname',
            'lastname',
            'abr_approved_by',
            'abr_approved_at',
            'abr_file_doc_no',
            'abr_checked_by',
            'abr_checked_by',
            'approved_budget_remark'
        );
    }

}
