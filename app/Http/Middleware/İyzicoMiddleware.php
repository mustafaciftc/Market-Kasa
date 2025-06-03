<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class İyzicoMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('/customer/callback')) {
            return $next($request); // CSRF kontrolünü atla
        }
        
        return app(\App\Http\Middleware\VerifyCsrfToken::class)->handle($request, $next);
    }
}
