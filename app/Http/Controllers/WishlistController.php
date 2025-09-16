<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index(Request $request)
    {
        // Trang danh sách yêu thích của user hiện tại
        $user = $request->user();
        $items = Wishlist::with(['product.images'])
            ->where('user_id', $user->id)
            ->latest()
            ->get();
        return view('pages.wishlist', compact('items'));
    }

    public function add(Request $request)
    {
        // Thêm sản phẩm vào wishlist (idempotent: không tạo trùng)
        $data = $request->validate([
            'product_id' => ['required','string','exists:products,id'],
        ]);

        $user = $request->user();

        $exists = Wishlist::where('user_id', $user->id)
            ->where('product_id', $data['product_id'])
            ->exists();

        if (! $exists) {
            Wishlist::create([
                'user_id' => $user->id,
                'product_id' => $data['product_id'],
            ]);
        }

        return back()->with('status', 'Đã thêm vào yêu thích.');
    }
}
