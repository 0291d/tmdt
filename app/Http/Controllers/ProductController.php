<?php

namespace App\Http\Controllers;

use App\Models\Product;

class ProductController extends Controller
{
    public function show(Product $product)
    {
        $product->load(['images', 'detail']);
        return view('pages.product.show', compact('product'));
    }
}

