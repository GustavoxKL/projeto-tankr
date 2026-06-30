<?php

namespace App\Http\Controllers;

use App\Models\Veiculo;
//use App\Http\Controllers\Controller;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VeiculoController extends Controller
{
    // Listar
    public function index()
    {
        $query = Veiculo::with('empresa');

        if (Auth::check() && Auth::user()->TipoUser === 'admin') {
            $query->where('FK_EMPRESA_ID_EMPRESA', Auth::user()->FK_EMPRESA_ID_EMPRESA);
        }

        return $query->get();
    }

    // Buscar
    public function show(Veiculo $veiculo)
    {
        if (Auth::check() && Auth::user()->TipoUser === 'admin') {
            if ($veiculo->FK_EMPRESA_ID_EMPRESA !== Auth::user()->FK_EMPRESA_ID_EMPRESA) {
                return response()->json(['message' => 'Acesso não autorizado'], 403);
            }
        }

        return $veiculo->load('empresa');
    }

    // Criar
    public function store(Request $request)
    {   
        try {
            
            if ($request->has('PlacaVei')) {
                $request->merge([
                    'PlacaVei' => preg_replace('/[^A-Z0-9]/', '', strtoupper($request->PlacaVei))
                ]);
            }

            if (Auth::check() && Auth::user()->TipoUser === 'admin') {
                $request->merge([
                    'FK_EMPRESA_ID_EMPRESA' => Auth::user()->FK_EMPRESA_ID_EMPRESA
                ]);
            }

            $data = $request->validate([
                'PlacaVei' => 'required|string|max:7|unique:veiculo,PlacaVei',
                'ModeloVei' => 'required|string|max:50',
                'AnoVei' => 'required|integer|min:1900|max:' . (date('Y') + 1),
                'FK_EMPRESA_ID_EMPRESA' => 'required|integer|exists:empresa,ID_EMPRESA'
            ]);

            $data['StatusVei'] = 1;
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

        if (Auth::check() && Auth::user()->TipoUser === 'admin') {
            if ($veiculo->FK_EMPRESA_ID_EMPRESA !== Auth::user()->FK_EMPRESA_ID_EMPRESA) {
                return response()->json(['message' => 'Acesso não autorizado'], 403);
            }

            $request->request->remove('FK_EMPRESA_ID_EMPRESA');
        }

        if ($request->has('PlacaVei')) {
            $request->merge([
                'PlacaVei' => preg_replace('/[^A-Z0-9]/', '', strtoupper($request->PlacaVei))
            ]);
        }

        $data = collect($request->validate([
            'PlacaVei' => 'sometimes|required|string|size:7|unique:veiculo,PlacaVei,' . $veiculo->ID_VEICULO . ',ID_VEICULO',
            'ModeloVei' => 'sometimes|required|string|max:100',
            'AnoVei' => 'sometimes|required|integer|min:1900|max:' . (date('Y') + 1),
            'StatusVei' => 'sometimes|boolean',
            'FK_EMPRESA_ID_EMPRESA' => 'sometimes|integer|exists:empresa,ID_EMPRESA'
        ]));

        if ($data instanceof \Illuminate\Support\Collection) {
            $data = $data->toArray();
        }

        if (isset($data['StatusVei'])) {
            $data['StatusVei'] = ($data['StatusVei'] == 1 || $data['StatusVei'] === true) ? 1 : 0;
        }

        $data = array_filter($data, fn($value) => $value !== '');

        $veiculo->update($data);

        return response()->json([
            'message' => 'Veiculo atualizado com sucesso',
            'data' => $veiculo
        ]);
    }

    // Deletar
    public function destroy(Veiculo $veiculo)
    {
        if (Auth::check() && Auth::user()->TipoUser === 'admin') {
            if ($veiculo->FK_EMPRESA_ID_EMPRESA !== Auth::user()->FK_EMPRESA_ID_EMPRESA) {
                return response()->json(['message' => 'Acesso não autorizado'], 403);
            }
        }
        
        $veiculo->delete();
        return response()->json(['message' => 'Veiculo excluido!']);
    }

}
