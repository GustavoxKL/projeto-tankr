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
        $estacoes = EstacaoAbastecimento::with('empresa')
            ->orderBy('Token', 'asc')
            ->get();

        $empresas = Empresa::where('StatusEmpresa', true)
            ->withCount(['estacoes'])
            ->orderBy('NomeEmpresa', 'asc')
            ->get();

        $estacoesAgrupadas = $estacoes->groupBy('FK_EMPRESA_ID_EMPRESA');

        return view('superadmin.estacoes.index', compact('estacoes', 'empresas', 'estacoesAgrupadas'));
    }
}
