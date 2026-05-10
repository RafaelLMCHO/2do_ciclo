<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Materia extends Model
{
    protected $table = 'materia';
    protected $primaryKey = 'id_materia';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'carga_horaria',
        'distintivo',
        'id_campo',
    ];

    public function campo()
    {
        return $this->belongsTo(CampoSaberes::class, 'id_campo', 'id_campo');
    }

    public function cursosGestiones()
    {
        return $this->belongsToMany(Curso::class, 'materia_curso_gestion', 'id_materia', 'id_curso')
            ->withPivot('id_gestion', 'id_profesor');
    }
}
