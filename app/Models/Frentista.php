<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Frentista extends Model
{
    use HasFactory;

    protected $table = 'frentista';
    protected $primaryKey = 'ID_FRENTISTA';
    
    protected $keyType = 'string';
    public $incrementing = false;
    
    public $timestamps = false;

    public function getRouteKeyName()
    {
        return 'ID_FRENTISTA';
    }

    protected $fillable = [
        'ID_FRENTISTA',
        'NomeFren',
        'StatusFren',
        'DataCadastroFren',
        'FK_EMPRESA_ID_EMPRESA'
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'FK_EMPRESA_ID_EMPRESA', 'ID_EMPRESA');
    }
}
