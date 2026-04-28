<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Usuario extends Authenticatable
{
    use HasApiTokens, HasFactory;    

    protected $table = 'usuario';

    protected $primaryKey = 'ID_USER';

    public function getRouteKeyName()
    {
        return 'ID_USER';
    }

    public $timestamps = false;

    protected $fillable = [
        'NomeUser',
        'EnderecoUser',
        'TelefoneUser',
        'StatusUser',
        'EmailUser',
        'SenhaUser',
        'TipoUser',
        'DataCadastroUser',
        'FK_EMPRESA_ID_EMPRESA'
    ];

    public function empresa()
    {
        return $this->belongTo(Empresa::class, 'FK_EMPRESA_ID_EMPRESA', 'ID_EMPRESA');
    }

    // Método para verificar se usuário está ativo
    public function isAtivo()
    {
        return $this->ativo === true || $this->ativo === 1;
    }

    // Método para verificar se é super admin
    public function isSuperAdmin()
    {
        return $this->tipo === 'superadmin';
    }
}
