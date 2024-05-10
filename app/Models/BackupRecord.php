<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BackupRecord extends Model
{
    use HasFactory;
    protected $primaryKey = 'br_id';
}
