<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpecialExternalCustomer extends Model
{
    use HasFactory;

    protected $table= 'special_external_customer';

    protected $primaryKey= 'spcus_id';


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'spcus_by','user_id');
    }
    
}
