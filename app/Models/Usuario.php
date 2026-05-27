<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Usuario extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'usuario';
    protected $primaryKey = 'ID_USER';

    public $timestamps = false;

    public function getRouteKeyName()
    {
        return 'ID_USER';
    }

    protected $fillable = [
        'NomeUser',
        'EnderecoUser',
        'TelefoneUser',
        'StatusUser',
        'email',
        'password',
        'TipoUser',
        'DataCadastroUser',
        'FK_EMPRESA_ID_EMPRESA'
    ];

    protected $hidden = [
        'password'
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'FK_EMPRESA_ID_EMPRESA', 'ID_EMPRESA');
    }

    // Método para verificar se usuário está ativo
    public function isAtivo()
    {
        return $this->StatusUser === true || $this->StatusUser === 1;
    }

    // Método para verificar se é super admin
    public function isSuperAdmin()
    {
        return $this->TipoUser === 'superadmin';
    }
}
