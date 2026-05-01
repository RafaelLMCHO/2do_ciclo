<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paralelo extends Model
{
    protected $table = 'paralelo';
    protected $primaryKey = 'id_paralelo';
    public $timestamps = false;

    protected $fillable = ['descripcion'];

    public function materiaCursoGestionParalelo()
    {
        return $this->hasMany(MateriaCursoGestionParalelo::class, 'id_paralelo', 'id_paralelo');
    }
}
