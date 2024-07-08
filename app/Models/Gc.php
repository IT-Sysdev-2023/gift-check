<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gc extends Model
{
    use HasFactory;

    protected $table = 'gc';
    protected $primaryKey = 'gc_id';

    public function denomination(){
        return $this->belongsTo(Denomination::class, 'denom_id', 'denom_id');
    }
}
