<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminOrStaffMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && in_array(auth()->user()->role, ['admin', 'personel'])) {
            return $next($request);
        }

        return redirect('/')->with('error', 'Bu sayfaya erişim izniniz yok!');
    }
}