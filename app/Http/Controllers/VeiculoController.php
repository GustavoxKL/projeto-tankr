<?php

namespace App\Http\Controllers;

use App\Models\Veiculo;
//use App\Http\Controllers\Controller;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

class VeiculoController extends Controller
{
    // Listar
    public function index()
    {
        return Veiculo::all();
    }

    // Buscar
    public function show(Veiculo $veiculo)
    {
        return $veiculo->load('empresa');
    }

    // Criar
    public function store(Request $request)
    {   
        try {
            $data = $request->validate([
                'PlacaVei' => 'required|string|max:7|unique:veiculo,PlacaVei',
                'ModeloVei' => 'required|string|max:50',
                'AnoVei' => 'required|integer',
                'FK_EMPRESA_ID_EMPRESA' => 'required|integer'
            ]);

            $data['DataCadastroVei'] = now();

            Veiculo::create($data);

            return response()->json(['message' => 'Veiculo cadastrado com sucesso']);

        } catch (\Throwable $th) {
            return response()->json(['message' => 'Erro ao cadastrar Veiculo', 'error' => $th->getMessage()], 400);
        }
    }

    // Atualizar/Editar
    public function update(Request $request, Veiculo $veiculo)
    {

        $data = collect($request->validate([
            'PlacaVei' => 'sometimes|string|max:7|unique:veiculo,PlacaVei',
            'ModeloVei' => 'sometimes|string|max:50',
            'AnoVei' => 'sometimes|integer',
            'FK_EMPRESA_ID_EMPRESA' => 'sometimes|integer'
        ]))
        ->filter(fn($value) => !is_null($value) && $value !== '')
        ->toArray();

        $veiculo->update($data);

        return response()->json([
            'message' => 'Veiculo atualizado com sucesso',
            'data' => $veiculo
        ]);
    }

    // Deletar
    public function destroy(Veiculo $veiculo)
    {
        $veiculo->delete();
        return response()->json(['message' => 'Veiculo excluido!']);
    }

}
