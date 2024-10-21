<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Customer extends Model
{
    use HasFactory;
    const CREATED_AT = 'cus_created_at';
    const UPDATED_AT = 'cus_updated_at';
    protected $guarded = [];

    protected $primaryKey = 'cus_id';

    protected $appends = ['full_name'];


    public function fullName(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value, array $attributes) => Str::title("{$attributes['cus_fname']}, {$attributes['cus_mname']}  {$attributes['cus_lname']}  {$attributes['cus_namext']}")
        );
    }
}
