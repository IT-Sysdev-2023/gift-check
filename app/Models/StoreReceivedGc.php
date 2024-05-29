<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoreReceivedGc extends Model
{
    use HasFactory;

    protected $table= 'store_received_gc';

    protected $primaryKey= 'strec_id';

    public function gcReleased(): BelongsTo
    {
        return $this->belongsTo(GcRelease::class, 'strec_barcode', 're_barcode_no'); 
        // `gc_release`.`re_barcode_no` = `store_received_gc`.`strec_barcode`
    }
}
