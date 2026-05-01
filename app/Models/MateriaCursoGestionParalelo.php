<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MateriaCursoGestionParalelo extends Model
{
    protected $table = 'materia_curso_gestion_paralelo';
    public $incrementing = false;
    public $timestamps = false;
    protected $primaryKey = null;

    protected $fillable = [
        'id_materia',
        'id_gestion',
        'id_curso',
        'id_paralelo',
        'id_horario',
        'id_aula',
    ];

    public function horario()
    {
        return $this->belongsTo(Horario::class, 'id_horario', 'id_horario');
    }

    public function aula()
    {
        return $this->belongsTo(Aula::class, 'id_aula', 'id_aula');
    }

    public function paralelo()
    {
        return $this->belongsTo(Paralelo::class, 'id_paralelo', 'id_paralelo');
    }
}
