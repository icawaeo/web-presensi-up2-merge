<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateAnyGuard
{
    /**
     * Handle an incoming request.
     * Cek apakah user terautentikasi dengan SALAH SATU guard yang diberikan.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$guards
     */
    public function handle(Request $request, Closure $next, ...$guards): Response
    {
        if (empty($guards)) {
            $guards = [null]; // Gunakan guard default jika tidak ada yang dispesifikasikan
        }

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // Jika user terautentikasi dengan salah satu guard, set guard tersebut sebagai default untuk request ini
                Auth::shouldUse($guard);
                return $next($request); // Lanjutkan request
            }
        }

        // Jika tidak ada guard yang cocok, alihkan ke halaman login
        return redirect()->route('login');
    }
}