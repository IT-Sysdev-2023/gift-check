<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromoGcRequest extends Model
{
    use HasFactory;

    protected $table = 'promo_gc_request';
    protected $primaryKey = 'pgcreq_id';
    public $timestamps = false;
    protected $guarded = [];
    public function casts(): array
    {
        return [
            'pgcreq_datereq' => 'datetime',
            'pgcreq_dateneeded' => 'date'
        ];
    }
    public function userReqby()
    {
        return $this->belongsTo(User::class, 'pgcreq_reqby', 'user_id');
    }
    public function approvedReq()
    {
        return $this->hasOne(
            ApprovedRequest::class,
            'reqap_trid',
            'pgcreq_id'
        );
    }


    public function scopeSelectPromoRequest($builder)
    {
        $builder->selectRaw(
            "promo_gc_request.pgcreq_reqnum,
            promo_gc_request.pgcreq_reqby,
            promo_gc_request.pgcreq_datereq,
            promo_gc_request.pgcreq_id,
            promo_gc_request.pgcreq_dateneeded,
            promo_gc_request.pgcreq_total,
            promo_gc_request.pgcreq_group_status,
            (
                SELECT CONCAT(users.firstname, ' ', users.lastname)
                FROM approved_request
                INNER JOIN users ON users.user_id = approved_request.reqap_preparedby
                WHERE approved_request.reqap_trid = promo_gc_request.pgcreq_id
                AND approved_request.reqap_approvedtype='promo gc preapproved'
                LIMIT 1
            ) AS approved_by"
        );
    }
    public function scopeWhereFilterForApproved($query)
    {
        return $query->where('pgcreq_group', '!=', '')
            ->where('pgcreq_group_status', 'approved')
            ->where('pgcreq_status', 'approved');
    }
    public function scopeWhereFilterForPending($query)
    {
        return $query->where('pgcreq_group', '!=', '')
            ->where('pgcreq_group_status', '')
            ->orWhere('pgcreq_group_status', 'approved')
            ->where('pgcreq_status', 'pending');
    }

    public function scopeSearchFilter($query, $filter)
    {
        return $query->whereAny([
            'pgcreq_datereq',
            'pgcreq_reqby',
            'pgcreq_reqnum'
        ], 'LIKE', '%' . $filter->search . '%');
    }

    public function scopeSelectPendingRequest($query)
    {
        // dd($query->get()->toArray());
        $query->select(
            'pgcreq_id',
            'pgcreq_id',
            'pgcreq_reqnum',
            'pgcreq_reqby',
            'pgcreq_datereq',
            'pgcreq_dateneeded',
            'pgcreq_group',
            'pgcreq_total',
            'pgcreq_remarks',
            'pgcreq_group_status',
        );
    }

    public function promoitems()
    {
        return $this->hasMany(PromoGcRequestItem::class, 'pgcreqi_trid', 'pgcreq_id');
    }
}
