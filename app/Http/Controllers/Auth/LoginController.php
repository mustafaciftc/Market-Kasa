<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            
            return $this->authenticated($request, Auth::user());
        }

        return back()
            ->withInput($request->only('email', 'remember'))
            ->withErrors([
                'email' => __('auth.failed'),
            ]);
    }

    protected function authenticated(Request $request, $user)
    {
        if ($user->role === 'admin' || $user->role === 'personel') {
            return redirect()->route('dashboard');
        } 
        
        if ($user->role === 'customer') {
            return redirect()->route('customer.shopping');
        }

        Auth::logout();
        return back()->withErrors([
            'email' => 'Hesabınızın erişim izni bulunmamaktadır.'
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/login');
    }
}