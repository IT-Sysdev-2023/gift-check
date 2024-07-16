<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreRequestItem extends Model
{
    use HasFactory;

    protected $primaryKey = 'sri_id';

    public function denomination(){
        return $this->hasOne(Denomination::class, 'denom_id', 'sri_items_denomination');
    }
}
