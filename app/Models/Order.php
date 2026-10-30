<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $order_number
 * @property int $customer_id
 * @property int $store_id
 * @property float|string $total_amount
 * @property string $status
 * @property string $shipping_address
 * @property string|null $notes
 * @property-read User|null $customer
 * @property-read Store|null $store
 * @property-read Collection<int, OrderItem> $items
 * @property-read int|null $items_count
 */
class Order extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'PENDING';
    public const STATUS_PROCESSED = 'PROCESSED';
    public const STATUS_SHIPPED = 'SHIPPED';
    public const STATUS_DELIVERED = 'DELIVERED';
    public const STATUS_CANCELLED = 'CANCELLED';

    public const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_PROCESSED,
        self::STATUS_SHIPPED,
        self::STATUS_DELIVERED,
        self::STATUS_CANCELLED,
    ];

    protected $fillable = [
        'order_number',
        'customer_id',
        'store_id',
        'total_amount',
        'status',
        'shipping_address',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'customer_id' => 'integer',
            'store_id' => 'integer',
            'total_amount' => 'decimal:2',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = 'ORD-' . date('Ymd') . '-S' . ($order->store_id ?? 0) . '-' . strtoupper(Str::random(6));
            }
        });
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
