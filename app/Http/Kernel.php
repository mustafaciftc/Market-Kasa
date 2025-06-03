<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    protected $middleware = [
        \Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance::class,
        \Illuminate\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        \App\Http\Middleware\TrustProxies::class,
    ];

    protected $middlewareGroups = [
	'web' => [
        \App\Http\Middleware\EncryptCookies::class,
        \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \App\Http\Middleware\VerifyCsrfToken::class,		
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
		
		
    ],

    'api' => [
        'throttle:api',
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
    ],

    ];

    protected $middlewareAliases = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'verified' => \App\Http\Middleware\EnsureEmailIsVerified::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'csrf' => \App\Http\Middleware\VerifyCsrfToken::class,
        'encryptCookies' => \App\Http\Middleware\EncryptCookies::class,
        'startSession' => \Illuminate\Session\Middleware\StartSession::class,
        'shareErrorsFromSession' => \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        'checkForMaintenanceMode' => \Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance::class,
        'validateSignature' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'isAdmin' => \App\Http\Middleware\IsAdmin::class,
		'admin_or_staff' => \App\Http\Middleware\AdminOrStaffMiddleware::class,
    	'customer' => \App\Http\Middleware\CustomerMiddleware::class,   
		'public_callback' => \App\Http\Middleware\PublicCallback::class,
		'iyzico.callback' => \App\Http\Middleware\VerifyIyzicoCallback::class,



	];
}