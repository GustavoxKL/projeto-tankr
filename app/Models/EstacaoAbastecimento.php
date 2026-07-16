<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EstacaoAbastecimento extends Model
{
    use HasFactory;
    
    protected $table = 'estacaoabastecimento';

    protected $primaryKey = 'ID_ESTACAO';

    public function getRouteKeyName()
    {
        return 'ID_ESTACAO';
    }

    public $timestamps = false;

    protected $fillable = [
        'EnderecoEst',
        'Token',
        'FK_EMPRESA_ID_EMPRESA'
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'FK_EMPRESA_ID_EMPRESA', 'ID_EMPRESA');
    }

    public function tanques()
    {
        return $this->belongsToMany(
            Tanque::class,
            'tanque_estacao',
            'ID_ESTACAO',
            'ID_TANQUE'
        );
    }
}
