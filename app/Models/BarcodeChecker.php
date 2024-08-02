<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Gc;

class BarcodeChecker extends Model
{
    use HasFactory;

    protected $table = 'barcode_checker';

    protected $primaryKey = 'bcheck_barcode';

    protected $guarded = [];

    public $timestamps = false;

    public function users()
    {
        return $this->belongsTo(User::class,  'bcheck_checkby', 'user_id');
    }
    public function gc()
    {
        return $this->belongsTo(Gc::class, 'bcheck_barcode', 'barcode_no');
    }
    public function special()
    {
        return $this->belongsTo(SpecialExternalGcrequestEmpAssign::class, 'bcheck_barcode', 'spexgcemp_barcode');
    }

    public function scannedBy()
    {
        return $this->belongsTo(User::class, 'bcheck_checkby', 'user_id');
    }
}
