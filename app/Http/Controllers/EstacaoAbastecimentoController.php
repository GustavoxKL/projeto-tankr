<?php

namespace App\Http\Controllers;

use App\Models\EstacaoAbastecimento;
//use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class EstacaoAbastecimentoController extends Controller
{
    // LISTAR
    public function index()
    {
        return EstacaoAbastecimento::all();
    }

    // BUSCAR
    public function show(EstacaoAbastecimento $estacao)
    {
        return $estacao;
    }

    // CRIAR
    public function store(Request $request)
    {
        try {

            $data = $request->validate([
                'EnderecoEst' => 'required|string|max:100',
                'Token' => 'required|integer', 
                'FK_EMPRESA_ID_EMPRESA' => 'required|integer'
            ]);

            EstacaoAbastecimento::create($data);

            return response()->json(['message' => 'Estacao cadastrada com sucesso']);

        } catch (\Throwable $th) {
            return response()->json(['message' => 'Erro ao cadastrar Estacao', 'error' => $th->getMessage()], 400);
        }
        
    }
 
    // ATUALIZAR
    public function update(Request $request, EstacaoAbastecimento $estacao)
    {
        $estacao->update($request->all());
        return $estacao;
    }

    // DELETAR
    public function destroy(EstacaoAbastecimento $estacao)
    {
        $estacao->delete();
        return response()->noContent();
    }
}
