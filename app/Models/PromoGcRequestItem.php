<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromoGcRequestItem extends Model
{
    use HasFactory;

    protected $primaryKey = 'pgcreqi_id';
    public $timestamps = false;
    protected $guarded = [];

    public function denomination()
    {
        return $this->belongsTo(Denomination::class, 'pgcreqi_denom', 'denom_id' );
    }
}
