<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class PromogcPreapproved extends Model
{
    use HasFactory;

    protected $table= 'promogc_preapproved';

    protected $primaryKey= 'prapp_id';

    public function user()
    {
        return $this->belongsTo(User::class, 'prapp_by', 'user_id');
    }
}
