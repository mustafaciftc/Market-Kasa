<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
class CheckAdminOrStaff
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && in_array(auth()->user()->role, ['admin', 'personel'])) {
            return $next($request);
        }

        return Redirect::to('/')->with('error', 'Bu sayfaya erişim izniniz yok!');
    }
}