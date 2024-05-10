<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpecialExternalGcrequestEmpAssign extends Model
{
    use HasFactory;
    protected $table= 'special_external_gcrequest_emp_assign';

    protected $primaryKey= 'spexgcemp_id';
}
