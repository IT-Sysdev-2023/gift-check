<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromogcReleased extends Model
{
    use HasFactory;

    protected $table= 'promogc_released';
    protected $guarded = [];

    protected $primaryKey= 'prgcrel_id';

    public $timestamps = false;
}
