<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\News;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        $categoryNames = ['Sofa', 'Table', 'Bed', 'Armchair', 'Chair'];
        $idsByName = Category::whereIn('name', $categoryNames)->pluck('id', 'name');

        $newProducts = Product::with('images')->latest()->take(7)->get();
        $latestNews  = News::with('images')->latest()->take(4)->get();

        return view('pages.index', compact('idsByName', 'newProducts', 'latestNews'));
    }
}
