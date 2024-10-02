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

    public function scopeSelectFilterApproved($query){
        $query->select(
            'pe_id',
            'pe_num',
            'pe_requested_by',
            'pe_date_request',
            'pe_date_needed',
            'pe_file_docno',
            'pe_remarks',
            'pe_generate_code',
            'pe_requisition',
            'ape_approved_by',
            'ape_remarks',
            'ape_approved_at',
            'ape_preparedby',
            'ape_checked_by',
            'pe_type',
            'pe_group',
            'reqby.firstname as rname',
            'reqby.lastname as rsname',
            'appby.firstname as apname',
            'appby.lastname as apsurname',
        );
    }

}
