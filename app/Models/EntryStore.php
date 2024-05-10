<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntryStore extends Model
{
    use HasFactory;

    protected $table = 'entry_store';
    protected $primaryKey = 'es_no';
}
