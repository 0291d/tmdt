<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Coupon extends Model
{
    // Mã giảm giá theo phần trăm, có ngày hết hạn và giới hạn lượt dùng
    use HasFactory,HasUuids;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'code', 'discount_percent', 'expiry_date', 'max_uses', 'used_count'
    ];

    // Các đơn hàng đã áp dụng coupon
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
