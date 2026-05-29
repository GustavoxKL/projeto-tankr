<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
//use App\Http\Controllers\Controller;

class EmpresaController extends Controller
{
    // Listar
    public function index()
    {
        return Empresa::all();
    }

    // Buscar
    public function show(Empresa $empresa)
    {
        return $empresa;
    }

    // Criar
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'NomeEmpresa' => 'required|string|max:100',
                'CNPJ' => 'required|string|max:18|unique:empresa,CNPJ',
                'TelefoneEmpresa' => 'nullable|string|max:20',
                'EnderecoEmpresa' => 'nullable|string|max:200'
            ]);

            
            $data['StatusEmpresa'] = 1;
            $data['DataCadastroEmpresa'] = now();

            Empresa::create($data);

            return response()->json(['message' => 'Empresa cadastrado com sucesso']);

        } catch (\Throwable $th) {
            return response()->json(['message' => 'Erro ao cadastrar Empresa', 'error' => $th->getMessage()], 400);
        }
    }

    // Atualizar/Editar
    public function update(Request $request, Empresa $empresa)
    {
        $data = $request->validate([
            'NomeEmpresa' => 'sometimes|required|string|max:100',
            'CNPJ' => 'sometimes|required|string|max:18|unique:empresa,CNPJ,' . $empresa->ID_EMPRESA . ',ID_EMPRESA',
            'TelefoneEmpresa' => 'sometimes|nullable|string|max:15',
            'EnderecoEmpresa' => 'sometimes|nullable|string|max:200',
            'StatusEmpresa' => 'sometimes|boolean'
        ]);

        // Garantir que é array
        if ($data instanceof \Illuminate\Support\Collection) {
            $data = $data->toArray();
        }

        // Converter StatusEmpresa para boolean (0 ou 1)
        if (isset($data['StatusEmpresa'])) {
            $data['StatusEmpresa'] = ($data['StatusEmpresa'] == 1 || $data['StatusEmpresa'] === true) ? 1 : 0;
        }

        // Remover apenas strings vazias (mantém null)
        $data = array_filter($data, fn($value) => $value !== '');

        $empresa->update($data);

        return response()->json([
            'message' => 'Empresa atualizada com sucesso',
            'data' => $empresa
        ]);
    }

    // Deletar
    public function destroy(Empresa $empresa)
    {
        $empresa->delete();
        return response()->json(['message' => 'Empresa excluida!']);

        //implementar uma função que verifica se a empresa nn tem nd vinculado para permitir a exclusão
    }
}
