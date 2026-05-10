<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// CU09: Guarda los permisos o acciones especificas disponibles en cada modulo.
class Funcionalidad extends Model
{
    protected $table = 'funcionalidades';
    protected $primaryKey = 'id_funcionalidad';

    protected $fillable = [
        'id_modulo',
        'nombre',
        'descripcion',
    ];

    public function modulo()
    {
        return $this->belongsTo(Modulo::class, 'id_modulo', 'id_modulo');
    }
}
