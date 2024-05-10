<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForDenomSetUp extends Model
{
    use HasFactory;

    protected $table = 'for_denom_set_up';
    protected $primaryKey = 'fds_id';
}
