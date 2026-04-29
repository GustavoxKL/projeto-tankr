<?php

namespace App\Http\Controllers;

use App\Models\Veiculo;
//use App\Http\Controllers\Controller;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

class VeiculoController extends Controller
{
    // LISTAR
    public function index()
    {
        return Veiculo::all();
    }

    // BUSCAR
    public function show(Veiculo $veiculo)
    {
        return $veiculo;
    }

    // CRIAR
    public function store(Request $request)
    {   
        try {
            $data = $request->validate([
                'PlacaVei' => 'required|string|max:7',
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

    // ATUALIZAR
    public function update(Request $request, Veiculo $veiculo)
    {
        $veiculo->update($request->all());
        return $veiculo;
    }

    // DELETAR
    public function destroy(Veiculo $veiculo)
    {
        $veiculo->delete();
        return response()->noContent();
    }

}
