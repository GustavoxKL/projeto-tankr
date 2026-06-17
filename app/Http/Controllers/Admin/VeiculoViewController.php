<?php

namespace App\Http\Controllers\Admin;


use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Models\Veiculo;
use Illuminate\Support\Facades\Auth;

class VeiculoViewController extends Controller
{
    public function index()
    {
        $empresaId = Auth::user()->FK_EMPRESA_ID_EMPRESA;

        $veiculos = Veiculo::where('FK_EMPRESA_ID_EMPRESA', $empresaId)
            ->orderBy('ID_VEICULO', 'desc')
            ->get();

        return view('admin.veiculos.index', compact('veiculos'));
    }
}
