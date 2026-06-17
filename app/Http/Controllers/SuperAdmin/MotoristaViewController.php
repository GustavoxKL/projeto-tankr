<?php

namespace App\Http\Controllers\SuperAdmin;

// use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\Empresa;
use App\Models\Motorista;

class MotoristaViewController extends Controller
{
    public function index()
    {
        $motoristas = Motorista::with('empresa')
            ->orderBy('ID_MOTORISTA', 'desc')
            ->get();
        
        $empresas = Empresa::where('StatusEmpresa', true)
            ->orderBy('NomeEmpresa', 'asc')
            ->get();

        return view('superadmin.motoristas.index', compact('motoristas', 'empresas'));
    }
}
