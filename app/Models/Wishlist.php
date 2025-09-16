<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Wishlist extends Model
{
    // Danh sách yêu thích: user_id (số nguyên) + product_id (UUID)
    use HasFactory, HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id', 'product_id',
    ];

    // Chủ sở hữu wishlist
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Sản phẩm được thêm vào wishlist
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
