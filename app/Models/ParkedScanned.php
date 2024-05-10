<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParkedScanned extends Model
{
    use HasFactory;

    protected $table = 'parked_scanned';
    protected $primaryKey = 'ps_id';
}
