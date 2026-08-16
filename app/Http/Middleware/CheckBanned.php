<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Support\Facades\Auth;

class CheckBanned
{
    public function handle($request, Closure $next)
    {
        if (Auth::check() && (Auth::user()->status ?? 'active') === 'banned') {
            Auth::logout();
            return redirect()->route('login')->withErrors(['email' => 'تم حظر حسابك. يرجى مراجعة الإدارة.']);
        }
        return $next($request);
    }
}