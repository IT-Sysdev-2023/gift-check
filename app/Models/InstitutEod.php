<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class InstitutEod extends Model
{
    use HasFactory;

    protected $table = 'institut_eod';
    protected $primaryKey = 'ieod_id';

    protected function casts(): array
    {
        return [
            'ieod_date' => 'datetime'
        ];
    }

    public function scopeFilter(Builder $builder, $filter)
    {
        return $builder->when($filter['date'] ?? null, function ($query, $date) {
            $query->whereBetween('ieod_date', [$date[0], $date[1]]);
        })->when($filter['search'] ?? null, function ($query, $search) {
            $query->whereAny([
                'ieod_num',
            ], 'LIKE', '%' . $search . '%')->orWhereHas('user', function (Builder $query) use ($search) {
                $query->whereAny([
                    'firstname',
                    'lastname'
                ], 'LIKE', '%' . $search . '%');
            });
            ;
        });
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ieod_by', 'user_id');
    }
}
