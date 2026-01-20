<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderReturn extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'user_id',
        'status',
        'reason',
        'requested_at',
        'deadline_at',
        'approved_at',
        'rejected_at',
        'closed_at',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'deadline_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderReturnItem::class);
    }
}
