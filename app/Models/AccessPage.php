<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessPage extends Model
{
    use HasFactory;

    protected $table = 'access_page';
    protected $primaryKey = 'access_no';
}
