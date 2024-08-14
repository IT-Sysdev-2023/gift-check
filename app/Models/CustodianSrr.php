<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustodianSrr extends Model
{
    use HasFactory;

    protected $table = 'custodian_srr';
    protected $primaryKey = 'csrr_id';

    protected $guarded = [];

    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class, 'csrr_prepared_by', 'user_id');
    }
}
