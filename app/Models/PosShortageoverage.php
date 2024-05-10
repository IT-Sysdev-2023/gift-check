<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PosShortageoverage extends Model
{
    use HasFactory;

    protected $table= 'pos_shortageoverage';

    protected $primaryKey= 'stover_id';
}
