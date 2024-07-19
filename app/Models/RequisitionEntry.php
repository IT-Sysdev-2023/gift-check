<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequisitionEntry extends Model
{
    use HasFactory;

    protected $table = 'requisition_entry';

    protected $primaryKey = 'requis_is';

    public function productionRequest()
    {
        return $this->hasOne(ProductionRequest::class, 'pe_id', 'repuis_pro_id');
    }
    public function supplier()
    {
        return $this->hasOne(Supplier::class, 'gcs_id', 'requis_supplierid');
    }
    public function user()
    {
        return $this->hasOne(User::class, 'user_id', 'requis_req_by');
    }
}
