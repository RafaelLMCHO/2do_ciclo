<?php

namespace App\Domain\GestionAcademica\Services;

use App\Models\Gestion;
use App\Models\Nivel;
use App\Models\Turno;
use Illuminate\Database\Eloquent\Collection;

class GestionAcademicaService
{
    public function todasLasGestiones(): Collection
    {
        return Gestion::all();
    }

    public function crearGestion(string $nombre): Gestion
    {
        return Gestion::create(['nombre' => $nombre]);
    }

    public function actualizarGestion(Gestion $gestion, string $nombre): void
    {
        $gestion->nombre = $nombre;
        $gestion->save();
    }

    public function eliminarGestion(Gestion $gestion): void
    {
        $gestion->delete();
    }

    public function todosLosNiveles(): Collection
    {
        return Nivel::all();
    }

    public function crearNivel(string $nombre): Nivel
    {
        return Nivel::create(['nombre' => $nombre]);
    }

    public function actualizarNivel(Nivel $nivel, string $nombre): void
    {
        $nivel->nombre = $nombre;
        $nivel->save();
    }

    public function eliminarNivel(Nivel $nivel): void
    {
        $nivel->delete();
    }

    public function todosLosTurnos(): Collection
    {
        return Turno::all();
    }

    public function crearTurno(string $nombre): Turno
    {
        return Turno::create(['nombre' => $nombre]);
    }

    public function actualizarTurno(Turno $turno, string $nombre): void
    {
        $turno->nombre = $nombre;
        $turno->save();
    }

    public function eliminarTurno(Turno $turno): void
    {
        $turno->delete();
    }
}
