<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class StoreGcrequest extends Model
{
    use HasFactory;

    protected $table= 'store_gcrequest';

    protected $primaryKey= 'sgc_id';

    public function scopeCancelledGcRequest(Builder $query)
    {

     //   `store_gcrequest`.`sgc_id`,
	// 			`store_gcrequest`.`sgc_num`,
	// 			`cancelled_store_gcrequest`.`csgr_by`,
	// 			`cancelled_store_gcrequest`.`csgr_at`,
	// 			`stores`.`store_name`,
	// 			`store_gcrequest`.`sgc_requested_by`,
	// 			`users`.`firstname`,
	// 			`users`.`lastname`,
	// 			`store_gcrequest`.`sgc_date_request`

        return $query->with('user:user_id,firstname,lastname')
                    ->join('cancelled_store_gcrequest', 'sgc_id', 'csgr_gc_id')
                    ->join('stores', 'sgc_store', 'store_id')
                    ->where([['sgc_status', 0], ['sgc_cancel', '*']]);
    }
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
