<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
class ProductDetail extends Model
{
    // Thông số chi tiết cho 1 sản phẩm (kích thước, hoàn thiện, xuất xứ...)
    use HasFactory,HasUuids;
    public $incrementing = false;
    protected $keyType = 'string';

    protected $table = 'product_details'; 
    protected $fillable = ['product_id', 'width', 'length', 'height', 'origin', 'finishes'];

    // Quan hệ ngược: thuộc về 1 Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
