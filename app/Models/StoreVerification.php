<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class StoreVerification extends Model
{
    use HasFactory;

    protected $table = 'store_verification';

    protected $primaryKey = 'vs_id';

    protected $guarded = [];

    public $timestamps = false;

    protected function casts(): array{
        return [
            'vs_reverifydate' => 'date',
            'vs_date' => 'date'
        ];
    }

    public function user(){
       return $this->belongsTo(User::class, 'vs_by', 'user_id')->withDefault();
    }
    public function gc(){
       return $this->belongsTo(Gc::class, 'vs_barcode', 'barcode_no');
    }
    public function customer(){
      return  $this->belongsTo(Customer::class, 'vs_cn', 'cus_id')->withDefault();
    }
    public function type(){
       return $this->belongsTo(GcType::class, 'vs_gctype', 'gc_type_id')->withDefault();
    }
    public function store(){
        return $this->belongsTo(Store::class, 'vs_store', 'store_id')->withDefault();
    }
    public function trans_reval(){
       return $this->belongsTo(TransactionRevalidation::class, 'vs_barcode', 'reval_barcode');
    }
    public function scopeSelectFilter($query){

        $query->select(
            'vs_barcode',
            'vs_tf_denomination',
            'firstname',
            'lastname',
            'store_name',
            'vs_time',
            'vs_date',
            'vs_reverifydate',
            'cus_fname',
            'gctype',
        );

    }
}
