<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstitutTransaction extends Model
{
    use HasFactory;

    protected $primaryKey = 'institutr_id';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'institutr_date' => 'datetime'
        ];
    }


    public $timestamps = false;

    public function institutCustomer(){
        return $this->belongsTo(InstitutCustomer::class, 'institutr_cusid', 'ins_id');
    }
}
