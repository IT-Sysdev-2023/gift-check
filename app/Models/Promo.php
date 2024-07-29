<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Promo extends Model
{
    use HasFactory;

    protected $table = 'promo';
    protected $guarded=[];


    public $timestamps= false;

    protected $primaryKey = 'promo_id';

    public function scopeFilter(Builder $builder, $filter)
    {
        return $builder->when($filter['search'] ?? null, function ($query, $search) {
            $query->whereAny([
                'promo_name',
                'promo_id',
                'promo_group',
            ], 'LIKE', '%' . $search . '%');
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'promo_valby', 'user_id');
    }

}
