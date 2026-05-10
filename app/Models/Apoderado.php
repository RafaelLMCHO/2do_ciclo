<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// CU04: Modelo del tutor/apoderado.
class Apoderado extends Model
{
    // CU04: Tabla real de apoderados.
    protected $table = 'apoderado';
    // CU04: Llave primaria del apoderado.
    protected $primaryKey = 'id_apoderado';
    // CU04: La tabla apoderado no usa timestamps.
    public $timestamps = false;

    // CU04: Campos editables del tutor/apoderado.
    protected $fillable = [
        'ci',
        'nombres',
        'ap_paterno',
        'ap_materno',
        'genero',
        'ocupacion',
        'fecha_nac',
        'telefono',
    ];

    // CU04: Relaciona el apoderado con sus alumnos mediante parentesco.
    public function alumnos()
    {
        return $this->belongsToMany(Alumno::class, 'parentesco', 'id_apoderado', 'id_alumno')
            ->withPivot('descripcion');
    }
}
