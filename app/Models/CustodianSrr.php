<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustodianSrr extends Model
{
    use HasFactory;

    protected $table = 'custodian_srr';
    protected $primaryKey = 'csrr_id';

    public function requisEntry()
    {
        return $this->belongsTo(RequisitionEntry::class , 'csrr_requisition' ,'requis_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class,'csrr_prepared_by' ,'user_id');
    }

}
