<?php

namespace App\Http\Controllers\SuperAdmin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\Empresa;
use App\Models\Veiculo;

class VeiculoViewController extends Controller
{
    public function index()
    {
        
        $veiculos = Veiculo::with('empresa')
            ->orderBy('ID_VEICULO', 'desc')
            ->get();

        $empresas = Empresa::where('StatusEmpresa', true)
            ->orderBy('NomeEmpresa', 'asc')
            ->get();

        return view('superadmin.veiculos.index', compact('veiculos', 'empresas'));
    }
}
