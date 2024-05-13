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

    public function specialExternalCustomer(){
        return $this->belongsTo(SpecialExternalCustomer::class,'spexgc_company', 'spcus_id');
    }

     
}
