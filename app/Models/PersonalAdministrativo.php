<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// CU24: Personal administrativo con sus datos personales, laborales y usuario de acceso.
class PersonalAdministrativo extends Model
{
    protected $table = 'secretaria';
    protected $primaryKey = 'id_secretaria';

    public $timestamps = false;

    protected $fillable = [
        'id_secretaria',
        'nombre',
        'ap_paterno',
        'ap_materno',
        'direccion',
        'telefono',
        'correo',
        //'cargo',
        //'area',
        'fecha_ingreso',
        'id_user',
    ];

    protected $casts = [
        'fecha_ingreso' => 'date',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}
