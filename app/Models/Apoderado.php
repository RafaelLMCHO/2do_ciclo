<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Apoderado extends Model
{
    protected $table = 'apoderado';
    protected $primaryKey = 'id_apoderado';
    public $timestamps = false;

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

    public function alumnos()
    {
        return $this->belongsToMany(Alumno::class, 'parentesco', 'id_apoderado', 'id_alumno')
            ->withPivot('descripcion');
    }
}
