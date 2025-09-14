<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureAdmin
{
    public function handle(Request $request, Closure $next)
    {
        // Chỉ áp dụng kiểm tra cho khu vực Filament (mặc định /admin/*)
        if ($request->is('admin') || $request->is('admin/*')) {
            // Cho phép truy cập các trang đăng nhập/khôi phục mật khẩu của Filament mà không cần đăng nhập
            if ($request->is('admin/login') || $request->is('admin/password*')) {
                return $next($request);
            }

            if (!auth()->check()) {
                return redirect('/admin/login');
            }
            $role = strtolower((string) (auth()->user()->role ?? ''));
            if ($role !== 'admin') {
                return redirect()->route('home.index');
            }
        }

        return $next($request);
    }
}
