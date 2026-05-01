<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MateriaCursoGestion extends Model
{
    protected $table = 'materia_curso_gestion';
    public $incrementing = false;
    public $timestamps = false;
    protected $primaryKey = null;

    protected $fillable = [
        'id_materia',
        'id_gestion',
        'id_curso',
        'id_profesor',
    ];

    public function materia()
    {
        return $this->belongsTo(Materia::class, 'id_materia', 'id_materia');
    }

    public function gestion()
    {
        return $this->belongsTo(Gestion::class, 'id_gestion', 'id_gestion');
    }

    public function curso()
    {
        return $this->belongsTo(Curso::class, 'id_curso', 'id_curso');
    }

    public function profesor()
    {
        return $this->belongsTo(Profesor::class, 'id_profesor', 'id_profesor');
    }

    public function paralelos()
    {
        return $this->hasMany(MateriaCursoGestionParalelo::class, 'id_materia', 'id_materia')
            ->whereColumn('materia_curso_gestion_paralelo.id_gestion', 'materia_curso_gestion.id_gestion')
            ->whereColumn('materia_curso_gestion_paralelo.id_curso', 'materia_curso_gestion.id_curso');
    }
}
