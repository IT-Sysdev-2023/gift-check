<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentFund extends Model
{
    use HasFactory;

    protected $table= 'payment_fund';

    protected $primaryKey= 'pay_id';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pay_addby', 'user_id');
    }
}
