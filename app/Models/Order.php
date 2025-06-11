<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    // protected $appends = [
    //     'payment_method',
    //     'payment_status',
    //     'shipping_method',
    //     'user_id',
    //     'status',
    //     'total_amount',
    //     'discount_amount',
    //     'shipping_address',
    //     'notes',
    // ];

    protected $fillable = [
        'payment_method',
        'payment_status',
        'shipping_method',
        'user_id',
        'status',
        'total_amount',
        'discount_amount',
        'shipping_address',
        'notes',
    ];

    public function getTotalAttribute()
    {
        return $this->orderItems->sum(function ($item) {
            return $item->price * $item->quantity;
        });
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
