<?php

namespace App\Http\Controllers;

use App\Models\Frentista;
// use App\Http\Controllers\Controller;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FrentistaController extends Controller
{

    public function index()
    {
        $query = Frentista::with('empresa');

        if (Auth::check() && Auth::user()->TipoUser === 'admin') {
            $query->where('FK_EMPRESA_ID_EMPRESA', Auth::user()->FK_EMPRESA_ID_EMPRESA);
        }

        return $query->get();
    }

    public function show(Frentista $frentista)
    {
        if (Auth::check() && Auth::user()->TipoUser === 'admin') {
            if ($frentista->FK_EMPRESA_ID_EMPRESA !== Auth::user()->FK_EMPRESA_ID_EMPRESA) {
                return response()->json(['message' => 'Acesso não autorizado'], 403);
            }
        }

        return $frentista->load('empresa');
    }

   
    public function store(Request $request)
    {
        try {
            // Se for admin, forçar a empresa do user logado
            if (Auth::check() && Auth::user()->TipoUser === 'admin') {
                $request->merge([
                    'FK_EMPRESA_ID_EMPRESA' => Auth::user()->FK_EMPRESA_ID_EMPRESA
                ]);
            }

            $data = $request->validate([
                'ID_FRENTISTA' => 'required|string|max:50|unique:frentista,ID_FRENTISTA',
                'NomeFren' => 'required|string|max:100',
                'FK_EMPRESA_ID_EMPRESA' => 'required|integer|exists:empresa,ID_EMPRESA'
            ]);

            $data['StatusFren'] = 1;
            $data['DataCadastroFren'] = now();

            $frentista = Frentista::create($data);

            return response()->json([
                'message' => 'Frentista cadastrado com sucesso',
                'data' => $frentista
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Erro de validação',
                'errors' => $e->errors()
            ], 422);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Erro ao cadastrar frentista',
                'error' => $th->getMessage()
            ], 500);
        }
    }

   
    

   
    public function update(Request $request, Frentista $frentista)
    {
        if (Auth::check() && Auth::user()->TipoUser === 'admin') {
            if ($frentista->FK_EMPRESA_ID_EMPRESA !== Auth::user()->FK_EMPRESA_ID_EMPRESA) {
                return response()->json(['message' => 'Acesso não autorizado'], 403);
            }
            
            $request->request->remove('FK_EMPRESA_ID_EMPRESA');
        }

        $data = $request->validate([
            'NomeFren' => 'sometimes|required|string|max:100',
            'StatusFren' => 'sometimes|boolean',
            'FK_EMPRESA_ID_EMPRESA' => 'sometimes|required|exists:empresa,ID_EMPRESA'
        ]);

        if ($data instanceof \Illuminate\Support\Collection) {
            $data = $data->toArray();
        }

        if (array_key_exists('StatusFren', $data)) {
            $data['StatusFren'] = ($data['StatusFren'] == 1 || $data['StatusFren'] === true) ? 1 : 0;
        }

        $data = array_filter($data, function($value) {
            return $value !== '';
        });

        $frentista->update($data);

        return response()->json([
            'message' => 'Frentista atualizado com sucesso',
            'data' => $frentista->load('empresa')
        ]);
    }

   
    public function destroy(Frentista $frentista)
    {
        if (Auth::check() && Auth::user()->TipoUser === 'admin') {
            if ($frentista->FK_EMPRESA_ID_EMPRESA !== Auth::user()->FK_EMPRESA_ID_EMPRESA) {
                return response()->json(['message' => 'Acesso não autorizado'], 403);
            }
        }

        $frentista->delete();
        return response()->json(['message' => 'Frentista excluído!']);
    }
}
