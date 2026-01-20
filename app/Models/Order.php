<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id',
        'subtotal',
        'shipping_fee',
        'total',
        'shipping_address',
        'shipping_method',
        'payment_method',
        'order_notes',
        'status',
        'delivered_at',
        'completed_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'shipping_fee' => 'decimal:2',
        'total' => 'decimal:2',
        'delivered_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function shippingUpdates()
    {
        return $this->hasMany(OrderShippingUpdate::class)->orderBy('occurred_at')->orderBy('id');
    }

    public function returns()
    {
        return $this->hasMany(OrderReturn::class);
    }

    public function reviews()
    {
        return $this->hasMany(ProductReview::class);
    }

    public function getDisplayOrderNumberAttribute(): string
    {
        if (!empty($this->order_number)) {
            return (string) $this->order_number;
        }
        $date = $this->created_at ? $this->created_at->format('Ymd') : now()->format('Ymd');
        return 'CFG-' . $date . '-' . str_pad((string) $this->id, 6, '0', STR_PAD_LEFT);
    }
}
