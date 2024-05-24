<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreGcrequest extends Model
{
    use HasFactory;

    protected $table= 'store_gcrequest';

    protected $primaryKey= 'sgc_id';

    public function store(){
        return $this->belongsTo(Store::class, 'sgc_store','store_id' );
    }

    public function cancelledStoreGcRequest(){
        return $this->belongsTo(CancelledStoreGcrequest::class, 'sgc_id', 'csgr_gc_id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'sgc_requested_by', 'user_id');
    }
}
