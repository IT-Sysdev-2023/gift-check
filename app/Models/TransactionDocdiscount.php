<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionDocdiscount extends Model
{
    use HasFactory;

    protected $table= 'transaction_docdiscount';

    protected $primaryKey= 'trdocdisc_id';
}
