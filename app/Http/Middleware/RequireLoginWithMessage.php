<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RequireLoginWithMessage
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect()
                ->guest('/login')
                ->with('status', 'Bạn phải đăng nhập hoặc đăng ký để tiếp tục.');
        }

        return $next($request);
    }
}
