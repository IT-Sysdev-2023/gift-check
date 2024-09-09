<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreEod extends Model
{
    use HasFactory;

    protected $table= 'store_eod';

    protected $primaryKey= 'steod_id';


    protected $guarded = [];

    public $timestamps = false;

    public function store(){
        return $this->belongsTo(Store::class, 'steod_storeid', 'store_id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'steod_by', 'user_id');
    }
}
