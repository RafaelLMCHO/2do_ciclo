<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    protected $table = 'rol';
    protected $primaryKey = 'id_rol';
    public $timestamps = false;

    protected $fillable = [
        'tipo',
    ];

    public function funcionalidades()
    {
        return $this->belongsToMany(Funcionalidad::class, 'rol_funcionalidad', 'id_rol', 'id_funcionalidad');
    }
}
