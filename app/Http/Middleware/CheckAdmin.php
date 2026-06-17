<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (Auth::user()->TipoUser !== 'admin') {

            if (Auth::user()->TipoUser === 'superadmin') {
                return redirect()->route('superadmin.dashboard.index');
            }
            
            Auth::logout();
            return redirect()->route('login')->withErrors(['email' => 'Acesso não autorizado']);
        }

        
        if (!Auth::user()->FK_EMPRESA_ID_EMPRESA) {
            Auth::logout();
            return redirect()->route('login')->withErrors(['email' => 'Usuário sem empresa vinculada']);
        }

        return $next($request);
    }
}
