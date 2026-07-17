<?php

namespace App\Http\Controllers;

use App\Models\Tanque;
//use App\Http\Controllers\Controller;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TanqueController extends Controller
{
    
    public function index()
    {
        $query = Tanque::with(['empresa', 'estacoes']);

        if (Auth::check() && Auth::user()->TipoUser === 'admin') {
            $query->where('FK_EMPRESA_ID_EMPRESA', Auth::user()->FK_EMPRESA_ID_EMPRESA);
        }

        return $query->get();
    }


    public function show(Tanque $tanque)
    {
        if (Auth::check() && Auth::user()->TipoUser === 'admin') {
            if ($tanque->FK_EMPRESA_ID_EMPRESA !== Auth::user()->FK_EMPRESA_ID_EMPRESA) {
                return response()->json(['message' => 'Acesso não autorizado'], 403);
            }
        }

        return $tanque->load(['empresa', 'estacoes']);
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
                'NomeTanque' => 'required|string|max:50',
                'TipoCombustivelTanque' => 'required|in:Gasolina Comum,Gasolina Aditivada,Diesel S500,Diesel S10,Diesel S10 Aditivado,Etanol Comum,Etanol Aditivado,Arla 32',
                'CapacidadeMaxTanque' => 'required|numeric|min:1',
                'QuantidadeAtualTanque' => 'required|numeric|min:0',
                'FK_EMPRESA_ID_EMPRESA' => 'required|integer|exists:empresa,ID_EMPRESA',
                'estacoes' => 'nullable|array',
                'estacoes.*' => 'integer|exists:estacaoabastecimento,ID_ESTACAO'
            ]);

            // Validar se qtd atual não é maior que capacidade máxima
            if ($data['QuantidadeAtualTanque'] > $data['CapacidadeMaxTanque']) {
                return response()->json([
                    'message' => 'Erro de validação',
                    'errors' => ['QuantidadeAtualTanque' => ['Quantidade atual não pode ser maior que a capacidade máxima']]
                ], 422);
            }

            $estacoes = $data['estacoes'] ?? [];
            unset($data['estacoes']);

            $data['StatusTanque'] = 1;
            $data['DataCadastroTanque'] = now();

            $tanque = Tanque::create($data);

            // Vincular estações (relacionamento N:N)
            if (!empty($estacoes)) {
                $tanque->estacoes()->sync($estacoes);
            }

            return response()->json([
                'message' => 'Tanque cadastrado com sucesso',
                'data' => $tanque->load(['empresa', 'estacoes'])
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Erro de validação',
                'errors' => $e->errors()
            ], 422);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Erro ao cadastrar tanque',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    
    public function update(Request $request, Tanque $tanque)
    {
        if (Auth::check() && Auth::user()->TipoUser === 'admin') {
            if ($tanque->FK_EMPRESA_ID_EMPRESA !== Auth::user()->FK_EMPRESA_ID_EMPRESA) {
                return response()->json(['message' => 'Acesso não autorizado'], 403);
            }
            
            $request->request->remove('FK_EMPRESA_ID_EMPRESA');
        }

        $data = $request->validate([
            'NomeTanque' => 'sometimes|required|string|max:50',
            'TipoCombustivelTanque' => 'sometimes|required|in:Diesel S10,Diesel S500,Gasolina Comum,Gasolina Aditivada,Etanol,GNV',
            'CapacidadeMaxTanque' => 'sometimes|required|numeric|min:1',
            'QuantidadeAtualTanque' => 'sometimes|required|numeric|min:0',
            'StatusTanque' => 'sometimes|boolean',
            'FK_EMPRESA_ID_EMPRESA' => 'sometimes|required|exists:empresa,ID_EMPRESA',
            'estacoes' => 'nullable|array',
            'estacoes.*' => 'integer|exists:estacaoabastecimento,ID_ESTACAO'
        ]);

        if ($data instanceof \Illuminate\Support\Collection) {
            $data = $data->toArray();
        }

        // Validar se qtd atual não é maior que capacidade máxima
        $capacidade = $data['CapacidadeMaxTanque'] ?? $tanque->CapacidadeMaxTanque;
        $qtdAtual = $data['QuantidadeAtualTanque'] ?? $tanque->QuantidadeAtualTanque;
        
        if ($qtdAtual > $capacidade) {
            return response()->json([
                'message' => 'Erro de validação',
                'errors' => ['QuantidadeAtualTanque' => ['Quantidade atual não pode ser maior que a capacidade máxima']]
            ], 422);
        }

        $estacoes = $data['estacoes'] ?? null;
        unset($data['estacoes']);

        if (array_key_exists('StatusTanque', $data)) {
            $data['StatusTanque'] = ($data['StatusTanque'] == 1 || $data['StatusTanque'] === true) ? 1 : 0;
        }

        $data = array_filter($data, function($value) {
            return $value !== '';
        });

        $tanque->update($data);

        // Atualizar vínculo de estações
        if ($estacoes !== null) {
            $tanque->estacoes()->sync($estacoes);
        }

        return response()->json([
            'message' => 'Tanque atualizado com sucesso',
            'data' => $tanque->load(['empresa', 'estacoes'])
        ]);
    }

    
    public function destroy(Tanque $tanque)
    {
        if (Auth::check() && Auth::user()->TipoUser === 'admin') {
            if ($tanque->FK_EMPRESA_ID_EMPRESA !== Auth::user()->FK_EMPRESA_ID_EMPRESA) {
                return response()->json(['message' => 'Acesso não autorizado'], 403);
            }
        }

        // Remove os vínculos com estações antes (o cascade já deveria fazer, mas garantimos)
        $tanque->estacoes()->detach();
        $tanque->delete();
        
        return response()->json(['message' => 'Tanque excluído!']);
    }
    
}