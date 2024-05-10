<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovedGcrequest extends Model
{
    use HasFactory;

    protected $table = 'approved_gcrequest';
    protected $primaryKey = 'agcr_id';

    public function storeGcRequest(){
        return $this->belongsTo(StoreGcrequest::class, 'agcr_request_id', 'sgc_id');
    }
    
    public function user(){
        return $this->belongsTo(User::class, 'agcr_preparedby', 'user_id');
    }
}
