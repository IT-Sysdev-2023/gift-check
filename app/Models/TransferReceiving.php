<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferReceiving extends Model
{
    use HasFactory;

    protected $table= 'trans_receiving';

    protected $primaryKey= 'trans_recid';
}
