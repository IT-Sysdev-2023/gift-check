<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempSalesDiscountby extends Model
{
    use HasFactory;

    protected $table= 'temp_sales_discountby';

    protected $primaryKey= 'tsd_barcode';
}
