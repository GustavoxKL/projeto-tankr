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
        try {
            $data = $request->validate([
                'NomeEmpresa' => 'required|string|max:100',
                'CNPJ' => 'required|string|max:18|unique:empresa,CNPJ',
                'StatusEmpresa' => 'required|boolean',
                'TelefoneEmpresa' => 'nullable|string|max:20',
                'EnderecoEmpresa' => 'nullable|string|max:200'
            ]);

            $data['DataCadastroEmpresa'] = now();

            Empresa::create($data);

            return response()->json(['message' => 'Empresa cadastrado com sucesso']);

        } catch (\Throwable $th) {
            return response()->json(['message' => 'Erro ao cadastrar Empresa', 'error' => $th->getMessage()], 400);
        }
    }

    // ATUALIZAR
    public function update(Request $request, Empresa $empresa)
    {

        $data = collect($request->validate([
            'NomeEmpresa' => 'nullable|string|max:100',
            'CNPJ' => 'nullable|string|max:18|unique:empresa,CNPJ',
            'StatusEmpresa' => 'nullable|boolean',
            'TelefoneEmpresa' => 'nullable|string|max:15',
            'EnderecoEmpresa' => 'nullable|string|max:200'
        ]))
        ->filter(fn($value) => !is_null($value) && $value !== '')
        ->toArray();

        $empresa->update($data);

        return response()->json([
            'message' => 'Empresa atualizada com sucesso',
            'data' => $empresa
        ]);
    }

    // DELETAR
    public function destroy(Empresa $empresa)
    {
        $empresa->delete();
        return response()->json(['message' => 'Empresa excluida!']);

        //implementar uma função que verifica se a empresa nn tem nd vinculado para permitir a exclusão
    }
}
