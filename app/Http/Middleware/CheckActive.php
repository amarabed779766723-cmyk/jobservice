<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckActive
{
    public function handle($request, Closure $next)
    {
        if (Auth::check() && Auth::user()->is_active == 0) {
            Auth::logout();
            return redirect('/login')->with('suspended', 'حسابك موقّف مؤقتاً. يمكنك تفعيله من خلال تسجيل الدخول.');
        }

        return $next($request);
    }
}