<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesYreport extends Model
{
    use HasFactory;

    protected $table= 'sales_yreport';

    protected $primaryKey= 'yrep_id';
}
