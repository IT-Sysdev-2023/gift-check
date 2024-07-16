<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CancelledStoreGcrequest extends Model
{
    use HasFactory;

    protected $table = 'cancelled_store_gcrequest';
    protected $primaryKey = 'csgr_id';

    protected function casts(): array
    {
        return [
            'csgr_at' => 'datetime',
        ];
    }

    public function user(){
        return $this->belongsTo(User::class, 'csgr_by', 'user_id');
    }
}
