<?php

namespace App\Domain\Profesores\Services;

use App\Models\Profesor;
use App\Models\ProfesorPermiso;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ProfesorService
{
    public function eliminarProfesor(Profesor $profesor): void
    {
        DB::transaction(function () use ($profesor) {
            $pid = $profesor->id_profesor;

            $mcg = DB::table('materia_curso_gestion')
                ->where('id_profesor', $pid)
                ->get(['id_materia', 'id_gestion', 'id_curso']);

            foreach ($mcg as $row) {
                DB::table('nota')
                    ->where('id_materia', $row->id_materia)
                    ->where('id_gestion', $row->id_gestion)
                    ->where('id_curso', $row->id_curso)
                    ->delete();

                DB::table('materia_curso_gestion_paralelo')
                    ->where('id_materia', $row->id_materia)
                    ->where('id_gestion', $row->id_gestion)
                    ->where('id_curso', $row->id_curso)
                    ->delete();
            }

            DB::table('materia_curso_gestion')
                ->where('id_profesor', $pid)
                ->delete();

            DB::table('profesor_especialidad')
                ->where('id_profesor', $pid)
                ->delete();

            if ($profesor->permiso) {
                $profesor->permiso->delete();
            }

            $profesor->id_user = null;
            $profesor->save();

            $profesor->delete();
        });
    }

    public function crearPermisoDefecto(Profesor $profesor): void
    {
        ProfesorPermiso::create([
            'id_profesor' => $profesor->id_profesor,
            'puede_ver_horario' => false,
        ]);
    }

    public function generarUsernameDefault(Profesor $profesor): string
    {
        return 'profesor_' . str_pad((string) $profesor->id_profesor, 3, '0', STR_PAD_LEFT);
    }

    public function generarPasswordDefault(Profesor $profesor): string
    {
        return 'prof' . str_pad((string) $profesor->id_profesor, 3, '0', STR_PAD_LEFT);
    }
}
