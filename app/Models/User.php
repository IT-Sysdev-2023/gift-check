<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory, Notifiable;


    protected $primaryKey = 'user_id';
    protected $appends = ['full_name', 'format_firstname'];
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

    public function fullName(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value, array $attributes) => Str::title("{$attributes['firstname']} {$attributes['lastname']}")
        );
    }
    
    public function formatFirstname(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value, array $attributes) => ucwords($attributes['firstname'])
        );
    }

    public function accessPage()
    {
        return $this->belongsTo(AccessPage::class, 'usertype', 'access_no');
    }

    public function scopeUserTypeBudget(Builder $builder, $userType)
    {
        $builder->where('usertype', $userType)
                ->whereHas('budgetRequest', function ($query) { $query->where('br_request_status', 0); } );
    }

    public function userLog(): BelongsTo
    {
        return $this->belongsTo(Userlog::class, 'logs_userid', 'user_id');
    }

    public function budgetRequest()
    {
        return $this->hasMany(BudgetRequest::class, 'br_requested_by','user_id' );
    }
}
