<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempValidation extends Model
{
    use HasFactory;

    protected $table= 'temp_validation';

    protected $primaryKey= 'tval_barcode';
    protected $guarded = [];

    public $timestamps = false;
}
