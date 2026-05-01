<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Horario extends Model
{
    protected $table = 'horario';
    protected $primaryKey = 'id_horario';
    public $timestamps = false;

    protected $fillable = [
        'dia',
        'hora_inicio',
        'hora_fin',
    ];

    public function materiaCursoGestionParalelo()
    {
        return $this->hasMany(MateriaCursoGestionParalelo::class, 'id_horario', 'id_horario');
    }
}
