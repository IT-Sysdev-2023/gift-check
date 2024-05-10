<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BudgetRequest extends Model
{
    use HasFactory;

    protected $table = 'budget_request';
    protected $primaryKey = 'br_id';

    public function user(){
        return $this->belongsTo(User::class, 'br_requested_by', 'user_id' );
    }
}
