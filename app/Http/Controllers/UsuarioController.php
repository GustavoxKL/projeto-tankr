<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
//use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    // LISTAR TODOS
    public function index()
    {
        return Usuario::all();
    }

    // BUSCAR POR ID
    public function show(Usuario $usuario)
    {
        return $usuario;
    }

    // CRIAR 
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'NomeUser' => 'required|string|max:100',
                'EnderecoUser' => 'nullable|string|max:200',
                'TelefoneUser' => 'nullable|string|max:15',
                'StatusUser' => 'required|boolean',
                'email' => 'required|email|unique:usuario,EmailUser',
                'password' => 'required|min:6',
                'TipoUser' => 'nullable|string|max:50',
                'FK_EMPRESA_ID_EMPRESA' => 'required|integer'
            ]);
            
            $data['password'] = Hash::make($data['password']);
            $data['DataCadastroUser'] = now();

            Usuario::create($data);

            return response()->json(['message' => 'Usuário cadastrado com sucesso']);
            
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Erro ao cadastrar usuário', 'error' => $th->getMessage()], 400);
        }
    }

    // ATUALIZAR
    public function update(Request $request, Usuario $usuario)
    {
        $data = collect($request->validate([
            'NomeUser' => 'nullable|string|max:100',
            'EnderecoUser' => 'nullable|string|max:200',
            'TelefoneUser' => 'nullable|string|max:15',
            'StatusUser' => 'nullable|boolean',
            'EmailUser' => 'nullable|email|unique:usuario,EmailUser,' . $usuario->ID_USUARIO . ',ID_USUARIO',
            'SenhaUser' => 'nullable|min:6',
            'TipoUser' => 'nullable|string|max:50',
            'FK_EMPRESA_ID_EMPRESA' => 'nullable|integer'
        ]))
        ->filter(fn($value) => !is_null($value) && $value !== '')
        ->toArray();

        // criptografa senha SOMENTE se vier preenchida
        if (isset($data['SenhaUser'])) {
            $data['SenhaUser'] = Hash::make($data['SenhaUser']);
        }

        $usuario->update($data);

        return response()->json([
            'message' => 'Usuário atualizado com sucesso',
            'data' => $usuario
        ]);
    }

    // DELETAR
    public function destroy(Usuario $usuario)
    {
        $usuario->delete();
        return response()->json(['message' => 'Usuário excluido!']);
    }
 
}