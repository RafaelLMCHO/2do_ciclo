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
        // CU22: Ordena los anios escolares mas recientes primero.
        return Gestion::orderByDesc('nombre')->get();
    }

    public function crearGestion(string $nombre, string $fechaInicio, string $fechaFin): Gestion
    {
        // CU22: El documento indica que una gestion nueva inicia inactiva.
        return Gestion::create([
            'nombre' => $nombre,
            'fechainicio' => $fechaInicio,
            'fechafin' => $fechaFin,
            'activo' => false,
        ]);
    }

    public function actualizarGestion(Gestion $gestion, string $nombre, string $fechaInicio, string $fechaFin): void
    {
        $gestion->nombre = $nombre;
        $gestion->fechainicio = $fechaInicio;
        $gestion->fechafin = $fechaFin;
        $gestion->save();
    }

    public function activarGestion(Gestion $gestion): void
    {
        // CU22: Solo puede existir una gestion activa a la vez.
        Gestion::where('id_gestion', '!=', $gestion->id_gestion)->update(['activo' => false]);
        $gestion->activo = true;
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
