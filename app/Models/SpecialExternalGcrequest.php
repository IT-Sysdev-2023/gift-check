<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SpecialExternalGcrequest extends Model
{
    use HasFactory;

    protected $table = 'special_external_gcrequest';

    protected $primaryKey = 'spexgc_id';

    public function scopeSpexgcStatus(Builder $builder, mixed $request)
    {
        return $builder->where('spexgc_status', $request);
    }

    public function scopeSpexgcReleased(Builder $builder, mixed $request)
    {
        return $builder->where('spexgc_released', $request);
    }

    public function scopeSpexgcReviewed(Builder $builder, mixed $request)
    {

        return $builder->where('spexgc_reviewed', $request);
    }

    public function scopeSpexgcPromo(Builder $builder, mixed $request)
    {
        return $builder->where('spexgc_promo', $request);
    }

    public function scopeJoinSpecialExternalCustomer(Builder $builder)
    {
        return $builder->join('special_external_customer', 'special_external_gcrequest.spexgc_company', '=', 'special_external_customer.spcus_id');
    }

    public function scopeJoinApprovedRequest(Builder $builder)
    {
        return $builder->join('approved_request', 'special_external_gcrequest.spexgc_id', '=', 'approved_request.reqap_trid');
    }

    public function specialExternalCustomer()
    {
        return $this->belongsTo(SpecialExternalCustomer::class, 'spexgc_company', 'spcus_id');
    }
    public function specialExternalGcrequestItems()
    {
        return $this->hasOne(SpecialExternalGcrequestItem::class, 'specit_trid', 'spexgc_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'spexgc_reqby', 'user_id');
    }

    public function preparedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reqap_preparedby', 'user_id');
    }

    public function scopeReleasedGc(Builder $builder)
    {
        return $this->with([
            'user:user_id,firstname,lastname',
            'approvedRequest' => function ($query) {
                $query->selectColumn(['reqap_date', 'reqap_preparedby'])
                    ->approvedType('special external releasing');
            },
            'approvedRequest.user:user_id,firstname,lastname'
        ])
            ->joinSpecialExternalCustomer()
            ->select('spexgc_reqby', 'spexgc_id', 'spexgc_num', 'spexgc_datereq', 'spexgc_dateneed', 'spcus_acctname', 'spcus_companyname')
            ->spexgcReleased('released');
    }

    public function approvedRequest()
    {
        return $this->belongsTo(ApprovedRequest::class, 'spexgc_id', 'reqap_trid');
    }

    public function specialExternalBankPaymentInfo(): HasOne
    {
        return $this->hasOne(SpecialExternalBankPaymentInfo::class, 'spexgcbi_trid', 'spexgc_id');
    }

    public function document() : HasOne
    {
        return $this->hasOne(Document::class, 'doc_trid', 'spexgc_id');
    }
    public function specialExternalGcrequestEmpAssign() : HasMany 
    {
        return $this->hasMany(SpecialExternalGcrequestEmpAssign::class, 'spexgcemp_trid', 'spexgc_id');
    }

}
