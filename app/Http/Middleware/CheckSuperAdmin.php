<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckSuperAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (Auth::user()->TipoUser !== 'superadmin') {
            
            if (Auth::user()->TipoUser === 'admin') {
                return redirect()->route('admin.dashboard.index');
            }
            
            Auth::logout();
            return redirect()->route('login')->withErrors(['email' => 'Acesso não autorizado']);
        }

        return $next($request);
    }
}
