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
        return $motorista->load('empresa');
    }

    // Criar
    public function store(Request $request)
    {
        try {

            if ($request->has('TelefoneMot')) {
                $request->merge([
                    'TelefoneMot' => preg_replace('/\D/', '', $request->TelefoneMot)
                ]);
            }

            $data =$request->validate([
                'NomeMot' => 'required|string|max:100',
                'CNHMot' => 'nullable|string|max:11',
                'TelefoneMot' => 'nullable|string|max:11',
                'FK_EMPRESA_ID_EMPRESA' => 'required|integer|exists:empresa,ID_EMPRESA'
            ]);

            $data['DataCadastroMot'] = now();
            $data['StatusMot'] = 1;

            Motorista::create($data);

            return response()->json(['message' => 'Motorista cadastrado com sucesso']);

        } catch (\Throwable $th) {
            return response()->json(['message' => 'Erro ao cadastrar motorista', 'error' => $th->getMessage()], 400);
        }
    }

    // Atualizar/Editar
    public function update(Request $request, Motorista $motorista)
    {   
        if ($request->has('TelefoneMot')) {
            $request->merge([
                'TelefoneMot' => preg_replace('/\D/', '', $request->TelefoneMot)
            ]);
        }

        $data = $request->validate([
            'NomeMot' => 'sometimes|required|string|max:100',
            'CNHMot' => 'sometimes|nullable|string|max:11',
            'TelefoneMot' => 'sometimes|nullable|string|max:11',
            'StatusMot' => 'sometimes|boolean',
            'FK_EMPRESA_ID_EMPRESA' => 'sometimes|required|exists:empresa,ID_EMPRESA'
        ]);
        
        // Converter StatusMot para boolean (0 ou 1)
        if (isset($data['StatusMot'])) {
            $data['StatusMot'] = ($data['StatusMot'] == 1 || $data['StatusMot'] === true) ? 1 : 0;
        }

        // Garantir que é array
        if ($data instanceof \Illuminate\Support\Collection) {
            $data = $data->toArray();
        }

        // Remover apenas strings vazias (mantém null)
        $data = array_filter($data, fn($value) => $value !== '');

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