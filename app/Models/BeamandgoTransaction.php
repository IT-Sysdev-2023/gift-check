<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BeamandgoTransaction extends Model
{
    use HasFactory;

    protected $table = 'beamandgo_transaction';
    protected $primaryKey = 'bngver_id';
}
