<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarcodeChecker extends Model
{
    use HasFactory;

    protected $table = 'barcode_checker';
    protected $primaryKey = 'bcheck_barcode';
}
