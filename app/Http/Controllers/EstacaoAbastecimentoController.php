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
        return EstacaoAbastecimento::with('empresa')->get();
    }

    // Buscar
    public function show(EstacaoAbastecimento $estacao)
    {
        return $estacao->load('empresa');
    }

    // Criar
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'Token' => 'required|string|max:20|unique:estacaoabastecimento,Token',
                'EnderecoEst' => 'nullable|string|max:100',
                'FK_EMPRESA_ID_EMPRESA' => 'required|integer|exists:empresa,ID_EMPRESA'
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
            'Token' => 'sometimes|required|string|max:20|unique:estacaoabastecimento,Token,' . $estacao->ID_ESTACAO . ',ID_ESTACAO',
            'EnderecoEst' => 'sometimes|nullable|string|max:200',
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
