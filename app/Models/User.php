<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

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
        'permissions',
        'is_active',
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
            'permissions' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }

    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    public function canAccessAdminPage(string $page): bool
    {
        if ($this->role === 'admin') {
            return true;
        }

        if ($this->role === 'staff' && $this->is_active) {
            return in_array($page, $this->permissions ?? []);
        }

        return false;
    }

    /**
     * All admin page segments that can be granted to staff members.
     * The key must match URL segment(2) (e.g. /admin/gift-cards → 'gift-cards').
     * To add a new page: just add its segment key and a display label here.
     *
     * @return array<string, string>
     */
    public static function adminPages(): array
    {
        return [
            'dashboard' => 'Dashboard',
            'orders' => 'Orders',
            'products' => 'Products',
            'inventory' => 'Inventory',
            'users' => 'Users',
            'media' => 'Media',
            'blog' => 'Blog',
            'testimonials' => 'Testimonials',
            'gift-cards' => 'Gift Cards',
            'shipping' => 'Shipping',
            'rewards' => 'Rewards & Referral',
            'currencies' => 'Currencies',
            'settings' => 'Settings',
        ];
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
        return $this->hasMany(Order::class);
    }

    public function wishlist(): HasMany
    {
        return $this->hasMany(Wishlist::class);
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
                'code' => Referral::generateCode(),
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
