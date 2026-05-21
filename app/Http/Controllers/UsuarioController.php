<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
//use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    // Listar
    public function index()
    {
        return Usuario::all();
    }

    // Buscar
    public function show(Usuario $usuario)
    {
        return $usuario;
    }

    // Criar
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'NomeUser' => 'required|string|max:100',
                'EnderecoUser' => 'nullable|string|max:200',
                'TelefoneUser' => 'nullable|string|max:15',
                'StatusUser' => 'required|boolean',
                'email' => 'required|email|unique:usuario,email',
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

    // Atualizar/Editar
    public function update(Request $request, Usuario $usuario)
    {
        $data = collect($request->validate([
            'NomeUser' => 'sometimes|string|max:100',
            'EnderecoUser' => 'sometimes|string|max:200',
            'TelefoneUser' => 'sometimes|string|max:15',
            'StatusUser' => 'sometimes|boolean',
            'email' => 'sometimes|email|unique:usuario,email,' . $usuario->ID_USUARIO . ',ID_USUARIO',
            'password' => 'sometimes|min:6',
            'TipoUser' => 'sometimes|string|max:50',
            'FK_EMPRESA_ID_EMPRESA' => 'nullable|integer'
        ]))
        ->filter(fn($value) => !is_null($value) && $value !== '')
        ->toArray();

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $usuario->update($data);

        return response()->json([
            'message' => 'Usuário atualizado com sucesso',
            'data' => $usuario
        ]);
    }

    // Deletar
    public function destroy(Usuario $usuario)
    {
        $usuario->delete();
        return response()->json(['message' => 'Usuário excluido!']);
    }
 
}