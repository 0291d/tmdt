<?php
namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;

class ShopController extends Controller
{
    public function index()
    {
        // Trang shop tổng: có tham số q để tìm kiếm theo brand
        $q = request('q');

        $categories = Category::withCount('products')->get();

        $brands = Product::select('brand')
            ->whereNotNull('brand')
            ->groupBy('brand')
            ->selectRaw('brand, COUNT(*) as product_count')
            ->get();

        // Eager load images và sắp xếp ổn định theo mới nhất
        $productsQuery = Product::with('images')->latest();
        if ($q !== null && trim($q) !== '') {
            $term = trim($q);
            $productsQuery->where(function ($query) use ($term) {
                $like = '%'.$term.'%';
                $query->where('name', 'like', $like)
                      ->orWhere('brand', 'like', $like)
                      ->orWhere('description', 'like', $like);
            });
        }
        $products = $productsQuery->paginate(12)->appends(request()->query());

        return view('pages.shop', compact('categories', 'brands', 'products', 'q'));
    }

    public function category($id)
    {
        // Lọc theo danh mục
        $categories = Category::withCount('products')->get();
        $brands     = Product::select('brand')
            ->whereNotNull('brand')
            ->groupBy('brand')
            ->selectRaw('brand, COUNT(*) as product_count')
            ->get();

        $products = Product::with('images')
            ->where('category_id', $id)
            ->latest()
            ->paginate(12)
            // giữ nguyên điều kiện lọc khi chuyển trang 
            ->appends(request()->query());

        return view('pages.shop', compact('categories', 'brands', 'products'));
    }

    public function brand($brand)
    {
        // Lọc theo thương hiệu
        $categories = Category::withCount('products')->get();
        $brands     = Product::select('brand')
            ->whereNotNull('brand')
            ->groupBy('brand')
            ->selectRaw('brand, COUNT(*) as product_count')
            ->get();

        $products = Product::with('images')
            ->where('brand', $brand)
            ->latest()
            ->paginate(12)
            ->appends(request()->query());

        return view('pages.shop', compact('categories', 'brands', 'products'));
    }
}



