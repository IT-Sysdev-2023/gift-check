<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreLocalServer extends Model
{
    use HasFactory;

    protected $table= 'store_local_server';

    protected $primaryKey= 'stlocser_id';
}
