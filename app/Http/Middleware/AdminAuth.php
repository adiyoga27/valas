<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuth
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('admin.login');
        }

        if (!str_ends_with($user->email, '@gmail.com')) {
            Auth::logout();
            return redirect()->route('admin.login')->with('error', 'Email tidak diizinkan.');
        }

        if (!$user->hasVerifiedEmail()) {
            Auth::logout();
            return redirect()->route('admin.login')->with('error', 'Email belum diverifikasi.');
        }

        return $next($request);
    }
}
