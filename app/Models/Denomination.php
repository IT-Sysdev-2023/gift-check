<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Denomination extends Model
{
    use HasFactory;

    protected $table = 'denomination';
    protected $primaryKey = 'denom_id';

    public static function denomation(){
        return self::where([['denom_type', 'RSGC'], ['denom_status', 'active']])
        ->orderBy('denomination')->get();
    }
    public function getDenom()
    {
        return $this->hasMany(TempPromo::class, 'tp_den','denom_id');
    }
}
