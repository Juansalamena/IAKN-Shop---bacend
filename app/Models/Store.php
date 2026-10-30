<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $address
 * @property string|null $phone
 * @property bool $is_active
 * @property-read Collection<int, Product> $products
 * @property-read Collection<int, Order> $orders
 * @property-read Collection<int, User> $admins
 * @property-read int|null $products_count
 * @property-read int|null $orders_count
 * @property-read int|null $admins_count
 */
class Store extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'address',
        'phone',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($store) {
            if (empty($store->slug)) {
                $store->slug = Str::slug($store->name) . '-' . Str::random(5);
            }
        });
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function admins(): HasMany
    {
        return $this->hasMany(User::class, 'store_id')->where('role', 'store_admin');
    }
}
