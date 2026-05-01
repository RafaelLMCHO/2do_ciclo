<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Curso extends Model
{
    protected $table = 'curso';
    protected $primaryKey = 'id_curso';
    public $timestamps = false;

    protected $fillable = ['nombre'];

    public function gestiones()
    {
        return $this->belongsToMany(Gestion::class, 'curso_gestion', 'id_curso', 'id_gestion');
    }

    public function materias()
    {
        return $this->belongsToMany(Materia::class, 'materia_curso_gestion', 'id_curso', 'id_materia')
            ->withPivot('id_gestion', 'id_profesor');
    }
}
