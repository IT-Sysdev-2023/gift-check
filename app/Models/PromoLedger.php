<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromoLedger extends Model
{
    use HasFactory;

    protected $table= 'promo_ledger';

    protected $primaryKey= 'promled_id';
}
