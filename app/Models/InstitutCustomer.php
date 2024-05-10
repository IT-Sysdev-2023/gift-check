<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstitutCustomer extends Model
{
    use HasFactory;

    protected $table = 'institut_customer';
    protected $primaryKey = 'ins_id';
}
