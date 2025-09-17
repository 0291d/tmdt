<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
class Comment extends Model
{
    // Bình luận của người dùng cho sản phẩm
    use HasFactory,HasUuids;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'user_id', 'product_id', 'content'
    ];

    // Người dùng đã bình luận
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Sản phẩm được bình luận
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
