<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;

class ApprovedGcrequest extends Model
{
    use HasFactory;

    protected $table = 'approved_gcrequest';
    protected $primaryKey = 'agcr_id';

    protected function casts(): array
    {
        return [
            'agcr_approved_at' => 'date'
        ];
    }
    

    // protected function serializeDate(DateTimeInterface $date): string
    // {
    //     return $date->toDayDateTimeString();
    // }

    public function storeGcRequest(){
        return $this->belongsTo(StoreGcrequest::class, 'agcr_request_id', 'sgc_id');
    }
    
    public function user(){
        return $this->belongsTo(User::class, 'agcr_preparedby', 'user_id');
    }
}
