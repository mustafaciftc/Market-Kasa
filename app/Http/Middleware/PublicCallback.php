<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PublicCallback
{
    public function handle(Request $request, Closure $next)
    {
        // Log the request for debugging
        Log::info('PublicCallback middleware executed', [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'headers' => $request->headers->all(),
            'input' => $request->all(),
        ]);

        // Allow the request to proceed without CSRF verification
        return $next($request);
    }
}