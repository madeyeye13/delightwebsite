<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Referral;
use App\Models\RewardPoint;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
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

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }


    public function referral(): HasOne
    {
        return $this->hasOne(Referral::class);
    }
 
    public function rewardPoints(): HasMany
    {
        return $this->hasMany(RewardPoint::class);
    }
 
    public function orders(): HasMany
    {
        return $this->hasMany(\App\Models\Order::class);
    }
 
    public function wishlist(): HasMany
    {
        return $this->hasMany(\App\Models\Wishlist::class);
    }
 
    /**
     * Auto-create a referral code for every new user.
     */
    protected static function boot(): void
    {
        parent::boot();
 
        static::created(function (self $user) {
            Referral::create([
                'user_id' => $user->id,
                'code'    => Referral::generateCode(),
            ]);
        });
    }
 
    /**
     * Get the user's current reward point balance.
     */
    public function getRewardBalanceAttribute(): int
    {
        return RewardPoint::balanceFor($this->id);
    }



}
