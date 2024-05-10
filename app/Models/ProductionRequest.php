<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionRequest extends Model
{
    use HasFactory;

    protected $table= 'production_request';

    protected $primaryKey= 'pe_id';

    public function user(){
        return $this->belongsTo(User::class, 'pe_requested_by', 'user_id');
    }
}
