<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Curso extends Model
{
    protected $table = 'curso';
    protected $primaryKey = 'id_curso';
    public $timestamps = false;

    protected $fillable = ['nombre', 'grado', 'id_nivel', 'id_paralelo', 'id_turno'];

    public function gestiones()
    {
        return $this->belongsToMany(Gestion::class, 'curso_gestion', 'id_curso', 'id_gestion');
    }

    public function materias()
    {
        return $this->belongsToMany(Materia::class, 'materia_curso_gestion', 'id_curso', 'id_materia')
            ->withPivot('id_gestion', 'id_profesor');
    }

    public function paralelos()
    {
        return $this->belongsToMany(Paralelo::class, 'materia_curso_gestion_paralelo', 'id_curso', 'id_paralelo')->distinct();
    }

    public function nivel()
    {
        return $this->belongsTo(Nivel::class, 'id_nivel');
    }

    public function paralelo()
    {
        return $this->belongsTo(Paralelo::class, 'id_paralelo', 'id_paralelo');
    }

    public function turno()
    {
        return $this->belongsTo(Turno::class, 'id_turno');
    }
}
