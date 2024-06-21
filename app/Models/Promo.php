<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Promo extends Model
{
    use HasFactory;

    protected $table = 'promo';

    protected $primaryKey = 'promo_id';


    public function user()
    {
        return $this->belongsTo(User::class, 'promo_valby', 'user_id');
    }

}
