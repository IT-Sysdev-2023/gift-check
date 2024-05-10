<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempReval extends Model
{
    use HasFactory;

    protected $table= 'temp_reval';

    protected $primaryKey= 'treval_id';
}
