<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Motorista extends Model
{
    use HasFactory;

    protected $table = 'motorista';

    protected $primaryKey = 'ID_MOTORISTA';

    public function getRouteKeyName()
    {
        return 'ID_MOTORISTA';
    }

    public $timestamps = false;

    public $fillable = [
        'NomeMot', 
        'CNHMot',
        'TelefoneMot',
        'StatusMot',
        'DataCadastroMot',
        'FK_EMPRESA_ID_EMPRESA'
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'FK_EMPRESA_ID_EMPRESA', 'ID_EMPRESA');
    }
}
