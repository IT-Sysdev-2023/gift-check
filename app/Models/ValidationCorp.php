<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ValidationCorp extends Model
{
    use HasFactory;

    protected $table = 'validation_corp';

    protected $primaryKey = 'vc_id';
}
