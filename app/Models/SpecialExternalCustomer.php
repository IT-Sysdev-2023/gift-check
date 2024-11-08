<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class SpecialExternalCustomer extends Model
{
    use HasFactory;

    const UPDATED_AT = 'spcus_at';
    protected $primaryKey = 'spcus_id';
    protected $guarded = [];
    protected $table = 'special_external_customer';
    public $timestamps = false;

    public function scopeFilter(Builder $builder, $filter)
    {
        return $builder->when($filter['date'] ?? null, function ($query, $date) {
            $query->whereBetween('spcus_at', [$date[0], $date[1]]);
        })->when($filter['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('spcus_companyname', 'LIKE', '%' . $search . '%')
                    ->orWhere('spcus_acctname', 'LIKE', '%' . $search . '%')
                    ->orWhere('spcus_address', 'LIKE', '%' . $search . '%')
                    ->orWhere('spcus_cperson', 'LIKE', '%' . $search . '%')
                    ->orWhere('spcus_cnumber', 'LIKE', '%' . $search . '%');
            })->orWhereHas('user', function (Builder $query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('firstname', 'LIKE', '%' . $search . '%')
                        ->orWhere('lastname', 'LIKE', '%' . $search . '%');
                });
            });
        });
    }

    protected function casts(): array
    {
        return [
            'spcus_at' => 'datetime'
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'spcus_by', 'user_id');
    }
}
