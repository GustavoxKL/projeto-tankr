<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tanque extends Model
{
    use HasFactory;

    protected $table = 'tanque';
    protected $primaryKey = 'ID_TANQUE';
    
    public $timestamps = false;

    public function getRouteKeyName()
    {
        return 'ID_TANQUE';
    }

    protected $fillable = [
        'NomeTanque',
        'TipoCombustivelTanque',
        'CapacidadeMaxTanque',
        'QuantidadeAtualTanque',
        'DataUltAbastecimentoTanque',
        'StatusTanque',
        'DataCadastroTanque',
        'FK_EMPRESA_ID_EMPRESA'
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'FK_EMPRESA_ID_EMPRESA', 'ID_EMPRESA');
    }

    public function estacoes()
    {
        return $this->belongsToMany(
            EstacaoAbastecimento::class,
            'tanque_estacao',
            'ID_TANQUE',
            'ID_ESTACAO'
        );
    }
}
