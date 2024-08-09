<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\RequisitionFormDenomination;

class RequisitionForm extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $primaryKey = 'req_no';

    protected $table = 'requisition_form';

    public function requisFormDenom()
    {
        return $this->hasMany(RequisitionFormDenomination::class, 'form_id' ,'req_no');
    }
}
