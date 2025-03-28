<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;
    protected $table= 'supplier';

    protected $primaryKey= 'gcs_id';

    protected $guarded = [];

    public $timestamps= false;
}
