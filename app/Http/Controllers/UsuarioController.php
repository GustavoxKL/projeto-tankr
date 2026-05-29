<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
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
        return Usuario::with('empresa')->get();
    }

    // Buscar
    public function show(Usuario $usuario)
    {
        return $usuario->load('empresa');
    }

    // Criar
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'NomeUser' => 'required|string|max:100',
                'EnderecoUser' => 'nullable|string|max:200',
                'TelefoneUser' => 'nullable|string|max:15',
                'email' => 'required|email|unique:usuario,email',
                'password' => 'required|min:6',
                'TipoUser' => 'required|in:superadmin,admin,user',
                'FK_EMPRESA_ID_EMPRESA' => 'nullable|integer|exists:empresa,ID_EMPRESA'
            ]);
            
            $data['password'] = Hash::make($data['password']);
            $data['StatusUser'] = 1;
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
        $validated = $request->validate([
            'NomeUser' => 'sometimes|required|string|max:100',
            'EnderecoUser' => 'sometimes|nullable|string|max:200',
            'TelefoneUser' => 'sometimes|nullable|string|max:15',
            'StatusUser' => 'sometimes|boolean',
            'email' => 'sometimes|required|email|unique:usuario,email,' . $usuario->ID_USER . ',ID_USER',
            'password' => 'sometimes|nullable|string|min:6',
            'TipoUser' => 'sometimes|required|in:superadmin,admin,user',
            'FK_EMPRESA_ID_EMPRESA' => 'sometimes|nullable|exists:empresa,ID_EMPRESA'
        ]);

        // Garantir que é array
        $data = is_array($validated) ? $validated : (array) $validated;

        // Converter StatusUser para boolean
        if (isset($data['StatusUser'])) {
            $data['StatusUser'] = $data['StatusUser'] ? 1 : 0;
        }

        // Tratar senha
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        // Remover apenas strings vazias (mantém null)
        $data = array_filter($data, fn($value) => $value !== '');

        $usuario->update($data);

        return response()->json([
            'message' => 'Usuário atualizado com sucesso',
            'data' => $usuario->load('empresa')
        ]);
    }

    // Deletar
    public function destroy(Usuario $usuario)
    {
        $usuario->delete();
        return response()->json(['message' => 'Usuário excluido!']);
    }
 
}