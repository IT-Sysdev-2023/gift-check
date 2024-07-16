<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovedProductionRequest extends Model
{
    use HasFactory;

    protected $table = 'approved_production_request';
    protected $primaryKey = 'ape_id';

    protected function casts() : array
    {
        return [
            'ape_approved_at' => 'datetime'
        ];
    }
}
