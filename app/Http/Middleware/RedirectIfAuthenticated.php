<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? ['web', 'karyawan'] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                return $guard === 'karyawan'
                            ? redirect()->route('karyawan.dashboard')
                            : redirect()->route('admin.dashboard');
            }
        }
        
        return $next($request);
    }
}