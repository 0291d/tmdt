<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Order extends Model
{
    use HasFactory, HasUuids;

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

    // Tính và gán tổng từ giỏ hàng + coupon (session)
    public function fillTotalsFromCart(array $cart, ?array $appliedCoupon): self
    {
        [$subtotal, $percent, $discountAmount, $finalTotal] = self::totalsFromCart($cart, $appliedCoupon);
        $this->discount_percent = $percent;
        $this->discount_amount = $discountAmount;
        $this->attributes['total'] = $finalTotal;
        return $this;
    }

    // Hàm tiện ích tính toán tổng từ giỏ hàng + coupon
    public static function totalsFromCart(array $cart, ?array $appliedCoupon): array
    {
        $subtotal = 0;
        foreach ($cart as $it) {
            $subtotal += (int)($it['price'] ?? 0) * (int)($it['quantity'] ?? 1);
        }
        $percent = (int)($appliedCoupon['percent'] ?? 0);
        $discountAmount = $percent ? (int) floor($subtotal * $percent / 100) : 0;
        $finalTotal = max(0, $subtotal - $discountAmount);
        return [$subtotal, $percent, $discountAmount, $finalTotal];
    }
}

