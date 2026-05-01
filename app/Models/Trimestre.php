<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trimestre extends Model
{
    protected $table = 'trimestre';
    protected $primaryKey = 'id_trimestre';
    public $timestamps = false;

    public function notas()
    {
        return $this->hasMany(Nota::class, 'id_trimestre', 'id_trimestre');
    }
}
