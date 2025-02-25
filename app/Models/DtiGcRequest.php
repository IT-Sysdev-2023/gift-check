<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class DtiGcRequest extends Model
{
    //
    protected $guarded = [];
    protected $table = 'dti_gc_requests';


    public function scopeCompanyName(Builder $builder)
    {
        return $builder->join('special_external_customer', 'dti_gc_requests.dti_company', '=', 'special_external_customer.spcus_id');
    }

    public function scopeDenomination(Builder $builder)
    {
        return $builder->join('dti_gc_request_items', 'dti_gc_request_items.dti_trid', 'dti_gc_requests.dti_num');
    }

    public function dtiDocuments()
    {
        return $this->hasMany(DtiDocument::class, 'dti_trid', 'dti_num');
    }
    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'dti_reqby');
    }

<<<<<<< HEAD
    public function specialDtiGcrequestItemsHasMany(){
        return $this->hasMany(DtiGcRequest::class, 'dti_num', 'id');
    }

=======
    public function customer(){
        return $this->belongsTo(SpecialExternalCustomer::class, 'dti_company', 'spcus_id');
    }
>>>>>>> b56888ee756dbabbfe4c574881da9383d8deb71c
}
