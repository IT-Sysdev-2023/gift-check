<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LedgerCheck extends Model
{
    use HasFactory;

    protected $table= 'ledger_check';

    protected $primaryKey = 'cledger_id';

    protected function casts(): array
    {
        return [
            'cledger_datetime' => 'date'
        ];
    }
    public function user(): BelongsTo {
        return $this->belongsTo(User::class, 'c_posted_by', 'user_id');
    }
}
