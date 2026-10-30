<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string|null $google_id
 * @property string|null $avatar
 * @property string $password
 * @property string $role
 * @property int|null $store_id
 * @property string|null $phone
 * @property string|null $address
 * @property Carbon|null $email_verified_at
 * @property-read Store|null $store
 * @property-read Collection<int, Order> $orders
 * @property-read int|null $orders_count
 */
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'google_id',
        'avatar',
        'password',
        'role',
        'store_id',
        'phone',
        'address',
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
            'store_id' => 'integer',
        ];
    }

    public static function current(): ?self
    {
        $user = Auth::user();

        return $user instanceof self ? $user : null;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return in_array($this->role, ['super_admin', 'store_admin']);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isStoreAdmin(): bool
    {
        return $this->role === 'store_admin';
    }

    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'customer_id');
    }
}
