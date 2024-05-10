<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferRequestServed extends Model
{
    use HasFactory;

    protected $table= 'transfer_request_served';

    protected $primaryKey= 'tr_servedid';
}
