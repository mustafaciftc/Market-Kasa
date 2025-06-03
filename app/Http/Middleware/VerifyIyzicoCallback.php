<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyIyzicoCallback as Middleware;

class VerifyIyzicoCallback extends Middleware
{
    public function handle($request, Closure $next) {
    if ($request->header('X-Iyzico-Signature') !== config('services.iyzico.secret_hash')) {
        abort(403, 'Invalid callback signature.');
    }
    return $next($request);
}
}