<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequisitionFormDenomination extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $primaryKey = 'id';

    protected $table = 'requisition_form_denomination';

}
