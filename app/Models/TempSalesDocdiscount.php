<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempSalesDocdiscount extends Model
{
    use HasFactory;

    protected $table= 'temp_sales_docdiscount';

    protected $primaryKey= 'docdis_id';
}
