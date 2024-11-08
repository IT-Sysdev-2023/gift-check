<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditCard extends Model
{
    use HasFactory;
    const UPDATED_AT = 'ccard_modified';
    const CREATED_AT = 'ccard_created';
    protected $guarded = [];
    protected $primaryKey = 'ccard_id';
}
