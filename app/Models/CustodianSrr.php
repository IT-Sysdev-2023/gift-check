<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustodianSrr extends Model
{
    use HasFactory;

    protected $table = 'custodian_srr';
    protected $primaryKey = 'csrr_id';

    protected $guarded = [];

    public $timestamps = false;
    protected function casts(): array
    {
        return [
            'csrr_datetime' => 'datetime'
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'csrr_prepared_by', 'user_id');
    }
    public function requisition()
    {
        return $this->belongsTo(RequisitionEntry::class, 'csrr_requisition', 'requis_id');
    }
    public function purorderdetails(){
        return $this->belongsTo(PurchaseOrderDetail::class, 'csrr_id', 'purchorderdet_ref');
    }
}
