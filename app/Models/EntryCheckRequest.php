<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntryCheckRequest extends Model
{
    use HasFactory;

    protected $table = 'entry_check_request';
    protected $primaryKey = 'cr_no';
}
