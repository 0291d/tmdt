<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Order extends Model
{
    // Đơn hàng: tổng tiền, giảm giá; liên kết Customer, Coupon, OrderItems
    use HasFactory,HasUuids;
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'customer_id', 'coupon_id', 'status', 'discount_percent', 'discount_amount', 'total'
    ];

    // Khách hàng (hồ sơ) đặt đơn
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    // Mã giảm giá áp dụng (nếu có)
    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    // Danh sách item thuộc đơn hàng
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Ngăn không cho set thủ công từ form; tổng được tính từ OrderItem
    public function setTotalAttribute($value): void
    {
        // Bỏ qua gán thủ công; total được tính trong luồng xử lý tạo đơn
    }
}
