<?php

namespace App\Http\Controllers\Admin;


use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Models\Motorista;
use Illuminate\Support\Facades\Auth;

class MotoristaViewController extends Controller
{
    public function index()
    {
        $empresaId = Auth::user()->FK_EMPRESA_ID_EMPRESA;

        $motoristas = Motorista::where('FK_EMPRESA_ID_EMPRESA', $empresaId)
            ->orderBy('ID_MOTORISTA', 'desc')
            ->get();

        return view('admin.motoristas.index', compact('motoristas'));
    }
}
