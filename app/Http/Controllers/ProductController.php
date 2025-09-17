<?php

namespace App\Http\Controllers;

use App\Models\Product;

class ProductController extends Controller
{
    public function show(Product $product)
    {
        // Trang chi tiết sản phẩm: nạp ảnh + detail + danh sách bình luận kèm user
        $product->load(['images', 'detail']);
        $comments = $product->comments()->with('user')->latest()->get();
        return view('pages.product.show', compact('product', 'comments'));
    }
}
