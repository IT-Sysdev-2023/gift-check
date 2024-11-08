<?php

namespace App\Models;

use App\Helpers\NumberHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Denomination extends Model
{
    use HasFactory;
    const CREATED_AT = 'denom_datecreated';
    const UPDATED_AT = 'denom_dateupdated';

    protected $guarded =[];
    
    protected $table = 'denomination';
    protected $primaryKey = 'denom_id';

    protected $appends = ['denomination_format'];
    public function denominationFormat(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value, array $attributes) => NumberHelper::currency($attributes['denomination'])
        );
    }
    public static function denomation(){
        return self::where([['denom_type', 'RSGC'], ['denom_status', 'active']])
        ->orderBy('denomination')->get();
    }
    public function getDenom()
    {
        return $this->hasMany(TempPromo::class, 'tp_den','denom_id');
    }
    public function gc(){
        return $this->hasOne(Gc::class, 'denom_id');
    }
    public function productionRequestItems(){
        return $this->hasOne(ProductionRequestItem::class, 'pe_items_denomination', 'denom_id');
    }

    public function transactionSales(){
        return $this->hasMany(TransactionSale::class, 'sales_denomination', 'denom_id');
    }

    public function transactionSales2(){
        return $this->belongsTo(TransactionSale::class, 'denom_id', 'sales_denomination');
    }
}
