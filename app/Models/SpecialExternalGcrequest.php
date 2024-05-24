<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpecialExternalGcrequest extends Model
{
    use HasFactory;

    protected $table= 'special_external_gcrequest';

    protected $primaryKey= 'spexgc_id';

    public function scopeSpexgcStatus(Builder $builder, mixed $request){
        return $builder->where('spexgc_status', $request);
    }

    public function scopeSpexgcReleased(Builder $builder, mixed $request){
        return $builder->where('spexgc_released', $request);
    }

    public function scopeSpexgcReviewed(Builder $builder, mixed $request){

        return $builder->where('spexgc_reviewed', $request);
    }

    public function scopeSpexgcPromo(Builder $builder, mixed $request){
        return $builder->where('spexgc_promo', $request);
    }

    public function scopeJoinSpecialExternalCustomer(Builder $builder){
        return $builder->join('special_external_customer', 'special_external_gcrequest.spexgc_company', '=', 'special_external_customer.spcus_id');
    }

    public function scopeJoinApprovedRequest(Builder $builder)
    {
        return $builder->join('approved_request', 'special_external_gcrequest.spexgc_id' , '=','approved_request.reqap_trid');
    }

    public function specialExternalCustomer(){
        return $this->belongsTo(SpecialExternalCustomer::class,'spexgc_company', 'spcus_id');
    }
    public function user(): BelongsTo{
        return $this->belongsTo(User::class, 'spexgc_reqby', 'user_id');
    }

    public function preparedBy(): BelongsTo{
        return $this->belongsTo(User::class, 'reqap_preparedby', 'user_id');
    }

    // public function requestBy(): BelongsTo{
    //     return $this->belongsTo(User::class, 'spexgc_reqby', 'user_id');
    // }

    public function approvedRequest(){
        return $this->belongsTo(ApprovedRequest::class, 'spexgc_id', 'reqap_trid');
    }

     
}
