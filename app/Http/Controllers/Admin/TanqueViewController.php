<?php

namespace App\Http\Controllers\Admin;

//use App\Http\Controllers\Controller;
use Illuminate\Routing\Controller;
use App\Models\Tanque;
use App\Models\EstacaoAbastecimento;
use Illuminate\Support\Facades\Auth;

class TanqueViewController extends Controller
{
    public function index()
    {
        $empresaId = Auth::user()->FK_EMPRESA_ID_EMPRESA;

        // Buscar tanques da empresa com as estações vinculadas
        $tanques = Tanque::with('estacoes')
            ->where('FK_EMPRESA_ID_EMPRESA', $empresaId)
            ->orderBy('DataCadastroTanque', 'desc')
            ->get();

        // Buscar todas as estações da empresa (para os checkboxes no modal)
        $estacoes = EstacaoAbastecimento::where('FK_EMPRESA_ID_EMPRESA', $empresaId)
            ->orderBy('Token', 'asc')
            ->get();

        return view('admin.tanques.index', compact('tanques', 'estacoes'));
    }
}