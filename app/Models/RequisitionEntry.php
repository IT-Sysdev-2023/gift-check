<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequisitionEntry extends Model
{
    use HasFactory;

    protected $table= 'requisition_entry';

    protected $primaryKey= 'requis_is';
}
