<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequisitionCancelled extends Model
{
    use HasFactory;

    protected $table= 'requisition_cancelled';

    protected $primaryKey= 'rcan_id';
}
