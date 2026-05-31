<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// CU20 y CU14: Modelo de aula usada para ubicar horarios de clases.
class Aula extends Model
{
    // CU20: Tabla real de aulas.
    protected $table = 'aula';
    // CU20: Llave primaria del aula.
    protected $primaryKey = 'id_aula';
    // CU20: La tabla aula no usa timestamps.
    public $timestamps = false;

    // CU20: Tipo o descripcion del aula.
    protected $fillable = ['tipo'];

    // CU14: Horarios/asignaciones que usan esta aula.
    public function materiaCursoGestionParalelo()
    {
        return $this->hasMany(MateriaCursoGestionParalelo::class, 'id_aula', 'id_aula');
    }
}
