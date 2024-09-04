<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovedBudgetRequest extends Model
{
    use HasFactory;

    protected $table = 'approved_budget_request';
    protected $primaryKey = 'abr_id';

    protected $guarded = [];

    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'abr_approved_at' => 'date'
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'abr_prepared_by', 'user_id');
    }
}
