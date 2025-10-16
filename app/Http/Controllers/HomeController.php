<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\News;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        // Trang chủ: lấy id danh mục theo tên để tạo link nhanh ở grid
        $categoryNames = ['Sofa', 'Table', 'Bed', 'Armchair', 'Chair'];
        // truy vấn danh mục để hiển thị 
        $idsByName = Category::whereIn('name', $categoryNames)->pluck('id', 'name');

        // Sản phẩm mới và tin mới để hiển thị slider
        $newProducts = Product::with('images')->latest()->take(7)->get();
        $latestNews  = News::with('images')->latest()->take(4)->get();

        return view('pages.index', compact('idsByName', 'newProducts', 'latestNews'));
    }
}
