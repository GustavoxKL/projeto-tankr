<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
//use App\Http\Controllers\Controller;

class EmpresaController extends Controller
{
    // LISTAR
    public function index()
    {
        return Empresa::all();
    }

    // BUSCAR
    public function show(Empresa $empresa)
    {
        return $empresa;
    }

    // CRIAR
    public function store(Request $request)
    {
        $data = $request->validate([
            'NomeEmpresa' => 'required|string|max:100',
            'CNPJ' => 'required|string|max:18',
            'TelefoneEmpresa' => 'nullable|string|max:15',
            'EnderecoEmpresa' => 'nullable|string|max:200',
            'StatusEmpresa' => 'nullable|boolean'
        ]);

        $data['DataCadastroEmpresa'] = now();

        return Empresa::create($data);
    }

    // ATUALIZAR
    public function update(Request $request, Empresa $empresa)
    {
        $empresa->update($request->all());
        return $empresa;
    }

    // DELETAR
    public function destroy(Empresa $empresa)
    {
        $empresa->delete();
        return response()->noContent();
    }
}
