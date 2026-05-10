<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// CU10: Representa los modulos que agrupan funcionalidades del sistema.
class Modulo extends Model
{
    protected $table = 'modulos';
    protected $primaryKey = 'id_modulo';

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    public function funcionalidades()
    {
        return $this->hasMany(Funcionalidad::class, 'id_modulo', 'id_modulo');
    }
}
