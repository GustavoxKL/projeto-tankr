<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Veiculo extends Model
{   
    use HasFactory;

    protected $table = 'veiculo';

    protected $primaryKey = 'ID_VEICULO';

    public function getRouteKeyName()
    {
        return 'ID_VEICULO';
    }

    public $timestamps = false;

    protected $fillable = [
        'PlacaVei',
        'ModeloVei',
        'AnoVei',
        'DataCadastroVei',
        'FK_EMPRESA_ID_EMPRESA'
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'FK_EMPRESA_ID_EMPRESA', 'ID_EMPRESA');
    }

}
