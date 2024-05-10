<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromogcPreapproved extends Model
{
    use HasFactory;

    protected $table= 'promogc_preapproved';

    protected $primaryKey= 'prapp_id';
}
