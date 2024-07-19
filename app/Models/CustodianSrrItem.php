<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustodianSrrItem extends Model
{
    use HasFactory;
    protected $primaryKey = 'cssitem_barcode';

    public function custodiaSsr(){
        return $this->hasOne(CustodianSrr::class,  'csrr_id', 'cssitem_recnum');
    }
}
