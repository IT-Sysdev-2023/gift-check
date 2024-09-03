<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovedPromorequest extends Model
{
    use HasFactory;

    protected $table = 'approved_promorequest';
    protected $primaryKey = 'apr_id';

    protected $guarded = [];
    public $timestamps = false;
}
