<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
    public function user()
    {
        return $this->belongsTo(User::class, 'dti_reqby', 'user_id');
    }

    public function specialDtiGcrequestItemsHasMany()
    {
        return $this->hasMany(DtiGcRequestItem::class, 'dti_trid', 'dti_num');
    }

    public function customer()
    {
        return $this->belongsTo(SpecialExternalCustomer::class, 'dti_company', 'spcus_id');
    }

    public function specialExternalCustomer()
    {
        return $this->belongsTo(SpecialExternalCustomer::class, 'dti_company', 'spcus_id');
    }
    public function approvedRequestRevied()
    {
        return $this->belongsTo(DtiApprovedRequest::class, 'dti_num', 'dti_trid')->where('dti_approvedtype', 'special external gc review');
    }

    public function approvedRequest()
    {
        return $this->belongsTo(DtiApprovedRequest::class,  'dti_num', 'dti_trid');
    }
    public function specialDtiBarcodesHasMany(): HasMany
    {
        return $this->hasMany(DtiBarcodes::class,  'dti_trid', 'dti_num');
    }


    // public function user()
    // {
    //     return $this->belongsTo(User::class, 'user_id', 'dti_reqby');
    // }

    // public function specialDtiGcrequestItemsHasMany()
    // {
    //     return $this->hasMany(DtiGcRequest::class, 'dti_num', 'id');
    // }

    // public function customer()
    // {
    //     return $this->belongsTo(SpecialExternalCustomer::class, 'dti_company', 'spcus_id');
    // }
}
