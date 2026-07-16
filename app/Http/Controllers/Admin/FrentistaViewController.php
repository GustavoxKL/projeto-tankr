<?php

namespace App\Http\Controllers\Admin;

//use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;
use App\Models\Frentista;

class FrentistaViewController extends Controller
{
    public function index()
    {
        $empresaId = Auth::user()->FK_EMPRESA_ID_EMPRESA;

        $frentistas = Frentista::where('FK_EMPRESA_ID_EMPRESA', $empresaId)
            ->orderBy('DataCadastroFren', 'desc')
            ->get();

        return view('admin.frentistas.index', compact('frentistas'));
    }
}
