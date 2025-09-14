<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Product extends Model
{
    use HasUuids, HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'category_id', 'name', 'brand', 'price', 'stock', 'description'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function detail()
    {
        return $this->hasOne(ProductDetail::class);
    }

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
