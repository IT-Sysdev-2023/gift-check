<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;


    protected $primaryKey = 'user_id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function scopeUserTypeBudget(Builder $builder, $userType){
        $builder->where('usertype', $userType)
                ->whereHas('budgetRequest', function ($query) { $query->where('br_request_status', 0); } );
    }

    public function userLog(): BelongsTo{
        return $this->belongsTo(Userlog::class, 'logs_userid', 'user_id');
    }

    public function budgetRequest(){
        return $this->hasMany(BudgetRequest::class, 'br_requested_by','user_id' );
    }
}
