<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gestion extends Model
{
    protected $table='gestion';
    protected $primaryKey='id_gestion';
    protected $fillable = [
        'nombre',
        'fechainicio',
        'fechafin',
        'activo',
    ];

    protected $casts = [
        'fechainicio' => 'date',
        'fechafin' => 'date',
        'activo' => 'boolean',
    ];
}
