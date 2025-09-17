<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Customer extends Model
{
     // Hồ sơ khách hàng (địa chỉ, điện thoại) gắn với User
     use HasUuids, HasFactory;
     public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id', 'phone', 'address'
    ];

    // Chủ sở hữu hồ sơ
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Các đơn hàng của khách hàng
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // Các liên hệ do khách hàng tạo
    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }
}
