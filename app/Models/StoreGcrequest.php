<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class StoreGcrequest extends Model
{
    use HasFactory;

    protected $table = 'store_gcrequest';

    protected $primaryKey = 'sgc_id';

    protected $guarded = [];
    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'sgc_date_request' => 'datetime',
            'sgc_date_needed' => 'date',
        ];
    }

    public function scopeJoinCancelledGcStore(Builder $builder)
    {
        return $builder->with([
            'user:user_id,firstname,lastname',
            'cancelledStoreGcRequest.user:user_id,firstname,lastname',
            'store:store_id,store_name'
        ]);
    }

    public function scopeCancelledGcRequest(Builder $query)
    {
        return $query->joinCancelledGcStore()->select('sgc_id', 'sgc_num', 'sgc_requested_by', 'sgc_date_request', 'sgc_store')->where([['sgc_status', 0], ['sgc_cancel', '*']]);
    }

    public function scopePendingRequest(Builder $builder)
    {
        return $builder->with([
            'user:user_id,firstname,lastname',
            'store:store_id,store_name'
        ])
            // ->select('sgc_id', 'sgc_num', 'sgc_date_needed', 'sgc_date_request', 'sgc_status', 'sgc_store', 'sgc_requested_by')
            ->where(function (Builder $query) {
                $query->where('sgc_status', 1)
                    ->orWhere('sgc_status', 0);
            })
            ->where('sgc_cancel', '')
            ->orderByDesc('sgc_id');
    }
    public function store()
    {
        return $this->belongsTo(Store::class, 'sgc_store', 'store_id');
    }

    public function cancelledStoreGcRequest()
    {
        return $this->belongsTo(CancelledStoreGcrequest::class, 'sgc_id', 'csgr_gc_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'sgc_requested_by', 'user_id');
    }
}
