<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpecialExternalCustomer extends Model
{
    use HasFactory;

    protected $table= 'special_external_customer';

    protected $primaryKey= 'spcus_id';
}
