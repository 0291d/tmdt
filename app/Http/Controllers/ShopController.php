<?php
namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;

class ShopController extends Controller
{
    public function index()
    {
        $q = request('q');

        $categories = Category::withCount('products')->get();

        $brands = Product::select('brand')
            ->whereNotNull('brand')
            ->groupBy('brand')
            ->selectRaw('brand, COUNT(*) as product_count')
            ->get();

        $productsQuery = Product::with('images');
        if ($q !== null && trim($q) !== '') {
            $term = trim($q);
            $productsQuery->where(function ($query) use ($term) {
                $like = '%'.$term.'%';
                $query->where('name', 'like', $like)
                      ->orWhere('brand', 'like', $like)
                      ->orWhere('description', 'like', $like);
            });
        }
        $products = $productsQuery->paginate(12)->withQueryString();

        return view('pages.shop', compact('categories', 'brands', 'products', 'q'));
    }
    public function category($id)
    {
        $categories = Category::withCount('products')->get();
        $brands     = Product::select('brand')
            ->whereNotNull('brand')
            ->groupBy('brand')
            ->selectRaw('brand, COUNT(*) as product_count')
            ->get();

        $products = Product::with('images')->where('category_id', $id)->paginate(12);

        return view('pages.shop', compact('categories', 'brands', 'products'));
    }

    public function brand($brand)
    {
        $categories = Category::withCount('products')->get();
        $brands     = Product::select('brand')
            ->whereNotNull('brand')
            ->groupBy('brand')
            ->selectRaw('brand, COUNT(*) as product_count')
            ->get();

        $products = Product::with('images')->where('brand', $brand)->paginate(12);

        return view('pages.shop', compact('categories', 'brands', 'products'));
    }
}
