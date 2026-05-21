<?php

namespace App\Http\Controllers;

use App\Models\Motorista;
use Exception;
//use App\Http\Controllers\Controller;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MotoristaController extends Controller
{
    // Listar
    public function index()
    {
        return Motorista::all();
    }

    // Buscar
    public function show(Motorista $motorista)
    {
        return $motorista;
    }

    // Criar
    public function store(Request $request)
    {
        try {
            $data =$request->validate([
                'NomeMot' => 'required|string|max:100',
                'CPF' => 'required|string|max:14',
                'TelefoneMot' => 'nullable|string|max:15',
                'EmailMot' => 'required|email|unique:motorista,EmailMot',
                'SenhaMot' => 'required|min:6',
                'FK_EMPRESA_ID_EMPRESA' => 'required|integer'
            ]);

            $data['SenhaMot'] = Hash::make($data['SenhaMot']);
            $data['DataCadastroMot'] = now();

            Motorista::create($data);

            return response()->json(['message' => 'Motorista cadastrado com sucesso']);

        } catch (\Throwable $th) {
            return response()->json(['message' => 'Erro ao cadastrar motorista', 'error' => $th->getMessage()], 400);
        }
    }

    // Atualizar/Editar
    public function update(Request $request, Motorista $motorista)
    {
        $data = collect($request->validate([
            'NomeMot' => 'nullable|string|max:100',
            'CPF' => 'nullable|string|max:14',
            'TelefoneMot' => 'nullable|string|max:15',
            'EmailMot' => 'nullable|email|unique:motorista,EmailMot' . $motorista->ID_MOTORISTA . ',ID_MOTORISTA',
            'SenhaMot' => 'nullable|min:6',
            'FK_EMPRESA_ID_EMPRESA' => 'nullable|integer'
        ]))
        ->filter(fn($value) => !is_null($value) && $value !== '')
        ->toArray();

        if (isset($data['SenhaMot'])) {
            $data['SenhaMot'] = Hash::make($data['SenhaMot']);
        }

        $motorista->update($data);

        return response()->json([
            'message' => 'Motorista atualizado com sucesso',
            'data' => $motorista
        ]);
    }

    // Deletar
    public function destroy(Motorista $motorista)
    {
        $motorista->delete();
        return response()->json(['message' => 'Motorista excluido!']);
    }
    
}