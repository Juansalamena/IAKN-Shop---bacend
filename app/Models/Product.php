<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $store_id
 * @property int|null $category_id
 * @property string $name
 * @property string $sku
 * @property string|null $description
 * @property float|string $price
 * @property int $stock
 * @property string|null $image
 * @property bool $is_active
 * @property-read Store|null $store
 * @property-read Category|null $category
 * @property-read Collection<int, OrderItem> $orderItems
 */
class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'category_id',
        'name',
        'sku',
        'description',
        'price',
        'stock',
        'image',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'stock' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
