<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpecialExternalGcrequestItem extends Model
{
    use HasFactory;

    protected $primaryKey = 'specit_id';
    protected $guarded = [];
    public $timestamps = false;
}
