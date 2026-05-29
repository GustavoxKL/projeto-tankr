<?php

namespace App\Http\Controllers\SuperAdmin;

// use App\Http\Controllers\Controller;
use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class EmpresaViewController extends Controller
{
    public function index()
    {
        $empresas = Empresa::withCount(['motoristas', 'veiculos'])
            ->orderBy('ID_EMPRESA', 'desc')
            ->get();
        
        return view('superadmin.empresas.index', compact('empresas'));
    }
}
