<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Coupon extends Model
{
    use HasFactory,HasUuids;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'code', 'discount_percent', 'expiry_date', 'max_uses', 'used_count'
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
