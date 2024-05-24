<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function specialExternalCustomer(){
        return $this->belongsTo(SpecialExternalCustomer::class,'spexgc_company', 'spcus_id');
    }
    public function user(){
        return $this->belongsTo(User::class, 'spexgc_reqby', 'user_id');
    }

    public function approvedRequest(){
        return $this->belongsTo(ApprovedRequest::class, 'spexgc_id', 'reqap_trid');
    }

     
}
