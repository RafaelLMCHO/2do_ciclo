<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profesor extends Model
{
    protected $table = 'profesor';
    protected $primaryKey = 'id_profesor';
    public $timestamps = false;

    protected $fillable = [
        'id_user',
        'ci',
        'nombre',
        'ap_paterno',
        'ap_materno',
        'direccion',
        'genero',
        'fecha_nac',
        'rda',
        'telefono',
        'correo',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function permiso()
    {
        return $this->hasOne(ProfesorPermiso::class, 'id_profesor', 'id_profesor');
    }
}
