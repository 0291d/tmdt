<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Product extends Model
{
    // Sản phẩm: dùng UUID, có detail 1-1, images (morph), comments, orderItems, wishlists
    use HasUuids, HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $casts = [
        'price' => 'integer',
    ];
    protected $fillable = [
        'category_id', 'name', 'brand', 'price', 'stock', 'description'
    ];

    // Danh mục chứa sản phẩm
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Thông tin chi tiết (kích thước, xuất xứ...)
    public function detail()
    {
        return $this->hasOne(ProductDetail::class);
    }

    // Ảnh (polymorphic) của sản phẩm
    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    // Các bình luận cho sản phẩm
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    // Các item đơn hàng liên quan đến sản phẩm
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Các bản ghi yêu thích chứa sản phẩm này
    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }
}
