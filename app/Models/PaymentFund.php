<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class PaymentFund extends Model
{
    use HasFactory;

    protected $table = 'payment_fund';

    protected $primaryKey = 'pay_id';

    protected $guarded = [];
    public $timestamps = false;
    protected function casts(): array
    {
        return [
            'pay_dateadded' => 'datetime'
        ];
    }

    public function scopeFilter(Builder $builder, $filter)
    {
        return $builder->when($filter['date'] ?? null, function ($query, $date) {
            $query->whereBetween('pay_dateadded', [$date[0], $date[1]]);
        })->when($filter['search'] ?? null, function ($query, $search) {
            $query->where(
                'pay_desc',
                'LIKE',
                '%' . $search . '%'
            )
                ->orWhereHas('user', function (Builder $query) use ($search) {
                    $query->whereAny([
                        'firstname',
                        'lastname'
                    ], 'LIKE', '%' . $search . '%');
                });
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pay_addby', 'user_id');
    }
}
