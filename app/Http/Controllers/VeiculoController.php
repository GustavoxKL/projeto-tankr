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
        $data = $request->validate([
            'PlacaVei' => 'required|string|max:7',
            'ModeloVei' => 'required|string|max:50',
            'AnoVei' => 'required|integer',
            'FK_EMPRESA_ID_EMPRESA' => 'required|integer'
        ]);

        $data['DataCadastroVei'] = now();

        return Veiculo::create($data);
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
