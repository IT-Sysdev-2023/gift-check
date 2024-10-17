<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class InstitutCustomer extends Model
{
    use HasFactory;

    protected $table = 'institut_customer';
    protected $primaryKey = 'ins_id';

    protected function casts(): array
    {
        return [
            'ins_date_created'=> 'datetime',
        ];
    }

    public function scopeFilter(Builder $builder, $filter)
    {
        return $builder->when($filter['date'] ?? null, function ($query, $date) {
            $query->whereBetween('ins_date_created', [$date[0], $date[1]]);
        })->when($filter['search'] ?? null, function ($query, $search) {
            $query->whereAny([
                'ins_name',
                'ins_custype',
            ], 'LIKE', '%' . $search . '%')
                ->orWhereHas('gcType', function (Builder $query) use ($search) {
                    $query->where('gctype', 'like', '%' . $search . '%');
                })
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
        return $this->belongsTo(User::class, 'ins_by','user_id');
    }

    public function gcType(){
        return $this->belongsTo(GcType::class,'ins_gctype','gc_type_id');
    }
}
