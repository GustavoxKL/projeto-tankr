<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tanque extends Model
{
    protected $fillable = [
        'ID_TANQUE',
        'CapacidadeTotal',
        'DataCadastro',
        'QuantidadeAtual',
        'TipoComb',
        'DataUltAbastecimento',
        ''
    ];
}
