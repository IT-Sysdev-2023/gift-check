<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuppliergcItem extends Model
{
    use HasFactory;

    protected $table= 'suppliergc_item';

    protected $primaryKey= 'suppgci_id';
}
