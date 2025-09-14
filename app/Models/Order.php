<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Order extends Model
{
    use HasFactory,HasUuids;
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'customer_id', 'coupon_id', 'status', 'discount_percent', 'discount_amount', 'total'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Ngăn không cho set thủ công từ form; tổng được tính từ OrderItem
    public function setTotalAttribute($value): void
    {
        // ignore manual assignment; total is computed in OrderItem events
    }
}
