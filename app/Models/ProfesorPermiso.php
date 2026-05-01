<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfesorPermiso extends Model
{
    protected $table = 'profesor_permisos';
    public $timestamps = false;

    protected $fillable = [
        'id_profesor',
        'puede_ver_horario',
    ];

    protected $casts = [
        'puede_ver_horario' => 'boolean',
    ];

    public function profesor()
    {
        return $this->belongsTo(Profesor::class, 'id_profesor', 'id_profesor');
    }
}
