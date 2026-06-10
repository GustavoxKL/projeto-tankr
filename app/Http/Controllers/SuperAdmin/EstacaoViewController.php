<?php

namespace App\Http\Controllers\SuperAdmin;

// use App\Http\Controllers\Controller;
use App\Models\EstacaoAbastecimento;
use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class EstacaoViewController extends Controller
{
    public function index()
    {
        // Buscar todas as estações com empresa
        $estacoes = EstacaoAbastecimento::with('empresa')
            ->orderBy('ID_ESTACAO', 'desc')
            ->get();

        // Buscar empresas para o select e agrupamento
        $empresas = Empresa::where('StatusEmpresa', true)
            ->withCount(['estacoes'])
            ->orderBy('NomeEmpresa', 'asc')
            ->get();

        // Agrupar estações por empresa
        $estacoesAgrupadas = $estacoes->groupBy('FK_EMPRESA_ID_EMPRESA');

        return view('superadmin.estacoes.index', compact('estacoes', 'empresas', 'estacoesAgrupadas'));
    }
}
