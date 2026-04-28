<?php

namespace App\Http\Controllers\Api;


use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
     /**
     * Login de usuário
     * POST /api/auth/login
     */
    public function login(Request $request)
    {
        // Validar dados
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'O username é obrigatório',
            'password.required' => 'A senha é obrigatória'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        // Buscar usuário por username
        $usuario = Usuario::where('username', $request->username)->first();

        // Verificar se usuário existe
        if (!$usuario) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário não encontrado'
            ], 401);
        }

        // Verificar se usuário está ativo
        if (!$usuario->isAtivo()) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário inativo'
            ], 401);
        }

        // Verificar senha
        if (!Hash::check($request->password, $usuario->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Senha incorreta'
            ], 401);
        }

        // Gerar token
        $token = $usuario->createToken('auth_token')->plainTextToken;

        // Carregar dados da empresa se houver
        $usuario->load('empresa');

        return response()->json([
            'success' => true,
            'message' => 'Login realizado com sucesso!',
            'token' => $token,
            'user' => [
                'id' => $usuario->id,
                'username' => $usuario->username,
                'nome' => $usuario->nome,
                'email' => $usuario->email,
                'role' => $usuario->role,
                'tipo' => $usuario->tipo,
                'empresa_id' => $usuario->empresa_id,
                'empresa_nome' => $usuario->empresa ? $usuario->empresa->nome : null
            ]
        ], 200);
    }

    /**
     * Logout de usuário
     * POST /api/auth/logout
     */
    public function logout(Request $request)
    {
        // Revogar token atual
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout realizado com sucesso!'
        ], 200);
    }

    /**
     * Obter dados do usuário autenticado
     * GET /api/auth/me
     */
    public function me(Request $request)
    {
        $usuario = $request->user();
        $usuario->load('empresa');

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $usuario->id,
                'username' => $usuario->username,
                'nome' => $usuario->nome,
                'email' => $usuario->email,
                'role' => $usuario->role,
                'tipo' => $usuario->tipo,
                'empresa_id' => $usuario->empresa_id,
                'empresa_nome' => $usuario->empresa ? $usuario->empresa->nome : null,
                'ativo' => $usuario->ativo
            ]
        ], 200);
    }

    /**
     * Atualizar senha
     * POST /api/auth/change-password
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
        ], [
            'current_password.required' => 'A senha atual é obrigatória',
            'new_password.required' => 'A nova senha é obrigatória',
            'new_password.min' => 'A nova senha deve ter no mínimo 6 caracteres',
            'new_password.confirmed' => 'As senhas não conferem'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $usuario = $request->user();

        // Verificar senha atual
        if (!Hash::check($request->current_password, $usuario->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Senha atual incorreta'
            ], 401);
        }

        // Atualizar senha
        $usuario->password = Hash::make($request->new_password);
        $usuario->save();

        return response()->json([
            'success' => true,
            'message' => 'Senha alterada com sucesso!'
        ], 200);
    }

    /**
     * Registro de novo usuário (público)
     * POST /api/auth/register
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'empresa_id' => 'required|exists:empresas,id',
            'username' => 'required|string|max:255|unique:usuarios,username',
            'password' => 'required|string|min:6|confirmed',
            'nome' => 'required|string|max:255',
            'email' => 'required|email|unique:usuarios,email',
        ], [
            'empresa_id.required' => 'A empresa é obrigatória',
            'empresa_id.exists' => 'Empresa não encontrada',
            'username.required' => 'O username é obrigatório',
            'username.unique' => 'Este username já está em uso',
            'password.required' => 'A senha é obrigatória',
            'password.min' => 'A senha deve ter no mínimo 6 caracteres',
            'password.confirmed' => 'As senhas não conferem',
            'email.unique' => 'Este email já está cadastrado'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Criar usuário
        $usuario = Usuario::create([
            'empresa_id' => $request->empresa_id,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'nome' => $request->nome,
            'email' => $request->email,
            'role' => 'user',
            'tipo' => 'user',
            'ativo' => true
        ]);

        // Gerar token
        $token = $usuario->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Usuário registrado com sucesso!',
            'token' => $token,
            'user' => [
                'id' => $usuario->id,
                'username' => $usuario->username,
                'nome' => $usuario->nome,
                'email' => $usuario->email,
                'role' => $usuario->role,
                'tipo' => $usuario->tipo
            ]
        ], 201);
    }
}
