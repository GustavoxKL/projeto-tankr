<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Traits\HttpResponses;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{   
    use HttpResponses; 

    public function index()
    {
        return view('login');
    }

    // Login API
    public function login(Request $request)
    {   
        if (Auth::attempt($request->only('email', 'password'))) {
            return $this->success([
                'token' => $request->user()->createToken('auth_token')->plainTextToken
            ], 'Autorizado', 200);
        }

        return $this->error(null, 'Não Autorizado', 403);
    }

    // Logout API 
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->success(null, 'Token Revogado', 200);
    }
   

    // Login WEB
    public function loginWeb(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (!Auth::attempt($credentials)) {
            return back()->withErrors([
                'email' => 'Email ou senha inválidos.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        $token = $request->user()->createToken('auth_token')->plainTextToken;

        $user = Auth::user();

        session([
            'api_token' => $token,
            'user_id' => $user->ID_USER,
            'nome' => $user->NomeUser,
            'email' => $user->email,
            'tipo' => $user->TipoUser,
            'empresa_id' => $user->FK_EMPRESA_ID_EMPRESA
        ]);

        if ($user->TipoUser === 'admin' and $user->StatusUser === 1) {
            return redirect()->route('admin.dashboard.index');
        }

        if ($user->TipoUser === 'superadmin' and $user->StatusUser === 1) {
            return redirect()->route('superadmin.dashboard.index');
        }

        // Se não for admin nem superadmin
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->withErrors([
            'email' => 'Tipo de usuário não autorizado.',
        ]);
    }

    // Logout WEB
    public function logoutWeb(Request $request)
    {
        $token = session('api_token');

        if ($token) {
            $accessToken = PersonalAccessToken::findToken($token);

            if ($accessToken) {
                $accessToken->delete();
            }
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}