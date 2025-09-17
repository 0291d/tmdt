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
                ->with('status', 'B?n ph?i dang nh?p ho?c dang ky d? ti?p t?c.');
        }

        return $next($request);
    }
}

