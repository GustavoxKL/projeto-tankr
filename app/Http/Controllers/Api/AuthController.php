<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Usuario;
use App\Traits\HttpResponses;

class AuthController extends Controller
{   
    use HttpResponses; 

    public function index()
    {
        return view('login');
    }

    // Login de usuário - POST
    public function login(Request $request)
    {   
        if (Auth::attempt($request->only('email', 'password'))) {
            return $this->success([
                'token' => $request->user()->createToken('auth_token')->plainTextToken
            ], 'Authorized', 200);
        }

        return $this->error(null, 'Not Authorized', 403);

        //$user = Auth::user();
        
        //return view('dashboard');
    }

    // Logout de usuário - POST
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->success(null, 'Token Revoked', 200);
    }
   
}
