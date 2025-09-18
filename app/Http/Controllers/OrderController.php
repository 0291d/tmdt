<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Customer;

class OrderController extends Controller
{
    // Lịch sử mua hàng cho user đang đăng nhập
    public function history(Request $request)
    {
        $user = $request->user();
        $orders = collect();
        $customer = null;

        if ($user) {
            $customer = Customer::where('user_id', $user->id)->first();
            if ($customer) {
                $orders = Order::with(['items.product.images'])
                    ->where('customer_id', $customer->id)
                    ->orderByDesc('created_at')
                    ->get();
            }
        }

        return view('pages.orders.history', [
            'orders' => $orders,
            'customer' => $customer,
            'user' => $user,
        ]);
    }
}

