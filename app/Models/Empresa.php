<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Empresa extends Model
{
    use HasFactory;
    
    protected $table = 'empresa';
    protected $primaryKey = 'ID_EMPRESA';

    public function getRouteKeyName()
    {
        return 'ID_EMPRESA';
    }

    public $timestamps = false;

    protected $fillable = [
        'NomeEmpresa',
        'CNPJ',
        'TelefoneEmpresa',
        'EnderecoEmpresa',
        'StatusEmpresa',
        'DataCadastroEmpresa'
    ];

    // Relacionamentos
    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'FK_EMPRESA_ID_EMPRESA', 'ID_EMPRESA');
    }

    public function motoristas()
    {
        return $this->hasMany(Motorista::class, 'FK_EMPRESA_ID_EMPRESA', 'ID_EMPRESA');
    }

    public function veiculos()
    {
        return $this->hasMany(Veiculo::class, 'FK_EMPRESA_ID_EMPRESA', 'ID_EMPRESA');
    }

    public function estacoes()
    {
        return $this->hasMany(EstacaoAbastecimento::class, 'FK_EMPRESA_ID_EMPRESA', 'ID_EMPRESA');
    }

    public function frentistas()
    {
        return $this->hasMany(Frentista::class, 'FK_EMPRESA_ID_EMPRESA', 'ID_EMPRESA');
    }

    public function tanques()
    {
        return $this->hasMany(Tanque::class, 'FK_EMPRESA_ID_EMPRESA', 'ID_EMPRESA');
    }
}
