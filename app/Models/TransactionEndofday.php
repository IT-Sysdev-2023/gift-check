<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionEndofday extends Model
{
    use HasFactory;

    protected $table= 'transaction_endofday';

    protected $primaryKey= 'eod_id';
}
