<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovedRequest extends Model
{
    use HasFactory;

    protected $table = 'approved_request';
    protected $primaryKey = 'reqap_id';

    public function user(){
        return $this->belongsTo(User::class, 'reqap_preparedby', 'user_id');
    }
    public function scopeApprovedType(Builder $builder, $param)
    {
        $builder->where('reqap_approvedtype', $param);
    }
}
