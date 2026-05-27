<?php

namespace App\Http\Controllers\SuperAdmin;

// use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\Empresa;
use App\Models\Usuario;

class UsuarioViewController extends Controller
{
    public function index()
    {
        // Buscar todos os usuários com empresa
        $usuarios = Usuario::with('empresa')
            ->orderBy('ID_USER', 'desc')
            ->get();

        // Buscar empresas para o select do modal
        $empresas = Empresa::where('StatusEmpresa', true)
            ->orderBy('NomeEmpresa', 'asc')
            ->get();

        return view('superadmin.usuarios.index', compact('usuarios', 'empresas'));
    }
}
