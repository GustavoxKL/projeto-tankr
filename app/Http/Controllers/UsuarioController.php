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
                'EmailUser' => 'required|email|unique:usuario,EmailUser',
                'SenhaUser' => 'required|min:6',
                'TipoUser' => 'nullable|string|max:50',
                'FK_EMPRESA_ID_EMPRESA' => 'required|integer'
            ]);
            
            $data['SenhaUser'] = Hash::make($data['SenhaUser']);
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
        $data = $request->all();

        // criptografa senha
        if(isset($data['SenhaUser'])) {
            $data['SenhaUser'] = Hash::make($data['SenhaUser']);
        }

        $usuario->update($data);
        return $usuario;
    }


    // DELETAR
    public function destroy(Usuario $usuario)
    {
        $usuario->delete();
        return response()->noContent();
    }
 
}