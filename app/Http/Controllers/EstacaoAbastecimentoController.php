<?php

namespace App\Http\Controllers;

use App\Models\EstacaoAbastecimento;
//use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class EstacaoAbastecimentoController extends Controller
{
    // Listar
    public function index()
    {
        return EstacaoAbastecimento::all();
    }

    // Buscar
    public function show(EstacaoAbastecimento $estacao)
    {
        return $estacao;
    }

    // Criar
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'EnderecoEst' => 'required|string|max:100',
                'Token' => 'required|integer', 
                'FK_EMPRESA_ID_EMPRESA' => 'required|integer'
            ]);

            EstacaoAbastecimento::create($data);

            return response()->json(['message' => 'Estação cadastrada com sucesso']);

        } catch (\Throwable $th) {
            return response()->json(['message' => 'Erro ao cadastrar estação', 'error' => $th->getMessage()], 400);
        }
        
    }
 
    // Atualizar/Editar
    public function update(Request $request, EstacaoAbastecimento $estacao)
    {

        $data = collect($request->validate([
            'EnderecoEst' => 'sometimes|string|max:100',
            'Token' => 'sometimes|integer',
            'FK_EMPRESA_ID_EMPRESA' => 'sometimes|integer'
        ]))
        ->filter(fn($value) => !is_null($value) && $value !== '')
        ->toArray();

        $estacao->update($data);

        return response()->json([
            'message' => 'Estação atualizado com sucesso',
            'data' => $estacao
        ]);
    }

    // Deletar
    public function destroy(EstacaoAbastecimento $estacao)
    {
        $estacao->delete();
        return response()->json(['message' => 'Estação excluida!']);
    }
}
