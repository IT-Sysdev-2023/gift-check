<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntryProduction extends Model
{
    use HasFactory;

    protected $table = 'entry_production';
    protected $primaryKey = 'ep_no';
}
