<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstitutEod extends Model
{
    use HasFactory;

    protected $table= 'institut_eod';
    protected $primaryKey = 'ieod_id';
}
