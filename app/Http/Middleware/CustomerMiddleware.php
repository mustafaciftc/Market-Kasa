<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CustomerMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        Log::info('CustomerMiddleware executed', [
            'user' => Auth::user() ? Auth::user()->toArray() : null,
            'path' => $request->path(),
        ]);

        if (Auth::check()) {
            if (Auth::user()->role === 'customer') {
                return $next($request);
            } else {
                Log::warning('User with non-customer role tried to access customer route', [
                    'user_id' => Auth::id(),
                    'role' => Auth::user()->role,
                ]);
                return redirect()->route('dashboard')->with('error', 'Bu sayfaya yalnızca müşteriler erişebilir.');
            }
        }

        Log::info('Unauthenticated user tried to access customer route', [
            'path' => $request->path(),
        ]);
        return redirect()->route('login')->with('error', 'Bu sayfaya erişmek için giriş yapmalısınız.');
    }
}