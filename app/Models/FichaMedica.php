<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// CU23: Modelo de la ficha medica asociada a un estudiante.
class FichaMedica extends Model
{
    protected $table = 'ficha_medica';
    protected $primaryKey = 'id_ficha';
    public $timestamps = false;

    protected $fillable = [
        'tipo_sangre',
        'alergias',
        'contacto_emergencia',
        'telf_emerg',
        'id_alumno',
    ];

    // CU23: Estudiante al que pertenece la ficha medica.
    public function alumno()
    {
        return $this->belongsTo(Alumno::class, 'id_alumno', 'id_alumno');
    }
}
