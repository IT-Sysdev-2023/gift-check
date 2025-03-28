<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempReceivestore extends Model
{
    use HasFactory;

    protected $table= 'temp_receivestore';

    protected $primaryKey= 'trec_barcode';

    protected $guarded = [];

    public $timestamps = false;
}
