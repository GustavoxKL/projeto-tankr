<?php

namespace App\Http\Controllers;

use App\Models\Motorista;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class MotoristaController extends Controller
{
    // Listar
    public function index()
    {
        $query = Motorista::with('empresa');

        // Se for admin, filtrar pela empresa
        if (Auth::check() && Auth::user()->TipoUser === 'admin') {
            $query->where('FK_EMPRESA_ID_EMPRESA', Auth::user()->FK_EMPRESA_ID_EMPRESA);
        }

        return $query->get();
    }

    // Buscar
    public function show(Motorista $motorista)
    {
        if (Auth::check() && Auth::user()->TipoUser === 'admin') {
            if ($motorista->FK_EMPRESA_ID_EMPRESA !== Auth::user()->FK_EMPRESA_ID_EMPRESA) {
                return response()->json(['message' => 'Acesso não autorizado'], 403);
            }
        }

        return $motorista->load('empresa');
    }

    // Criar
    public function store(Request $request)
    {
        try {
            if ($request->has('TelefoneMot')) {
                $request->merge([
                    'TelefoneMot' => preg_replace('/\D/', '', $request->TelefoneMot)
                ]);
            }

            // Se for admin, forçar a empresa do user logado
            if (Auth::check() && Auth::user()->TipoUser === 'admin') {
                $request->merge([
                    'FK_EMPRESA_ID_EMPRESA' => Auth::user()->FK_EMPRESA_ID_EMPRESA
                ]);
            }

            $data = $request->validate([
                'NomeMot' => 'required|string|max:100',
                'CNHMot' => 'nullable|string|max:11',
                'TelefoneMot' => 'nullable|string|max:11',
                'FK_EMPRESA_ID_EMPRESA' => 'required|integer|exists:empresa,ID_EMPRESA'
            ]);

            $data['StatusMot'] = 1;
            $data['DataCadastroMot'] = now();

            $motorista = Motorista::create($data);

            return response()->json([
                'message' => 'Motorista cadastrado com sucesso',
                'data' => $motorista
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Erro de validação',
                'errors' => $e->errors()
            ], 422);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Erro ao cadastrar motorista',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    // Atualizar/Editar
    public function update(Request $request, Motorista $motorista)
    {
        if (Auth::check() && Auth::user()->TipoUser === 'admin') {
            if ($motorista->FK_EMPRESA_ID_EMPRESA !== Auth::user()->FK_EMPRESA_ID_EMPRESA) {
                return response()->json(['message' => 'Acesso não autorizado'], 403);
            }
            
            $request->request->remove('FK_EMPRESA_ID_EMPRESA');
        }

        if ($request->has('TelefoneMot')) {
            $request->merge([
                'TelefoneMot' => preg_replace('/\D/', '', $request->TelefoneMot)
            ]);
        }

        $data = $request->validate([
            'NomeMot' => 'sometimes|required|string|max:100',
            'CNHMot' => 'sometimes|nullable|string|max:11',
            'TelefoneMot' => 'sometimes|nullable|string|max:11',
            'StatusMot' => 'sometimes|boolean',
            'FK_EMPRESA_ID_EMPRESA' => 'sometimes|required|exists:empresa,ID_EMPRESA'
        ]);

        if ($data instanceof \Illuminate\Support\Collection) {
            $data = $data->toArray();
        }

        if (isset($data['StatusMot'])) {
            $data['StatusMot'] = ($data['StatusMot'] == 1 || $data['StatusMot'] === true) ? 1 : 0;
        }

        $data = array_filter($data, fn($value) => $value !== '');

        $motorista->update($data);

        return response()->json([
            'message' => 'Motorista atualizado com sucesso',
            'data' => $motorista->load('empresa')
        ]);
    }

    // Deletar
    public function destroy(Motorista $motorista)
    {
        if (Auth::check() && Auth::user()->TipoUser === 'admin') {
            if ($motorista->FK_EMPRESA_ID_EMPRESA !== Auth::user()->FK_EMPRESA_ID_EMPRESA) {
                return response()->json(['message' => 'Acesso não autorizado'], 403);
            }
        }

        $motorista->delete();
        return response()->json(['message' => 'Motorista excluído!']);
    }
}