<?php

namespace App\Domain\Alumnos\Services;

use App\Models\Alumno;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AlumnoService
{
    public function crearConUsuario(array $data): void
    {
        DB::transaction(function () use ($data) {
            $usuario = User::create([
                'username' => $data['username'],
                'password' => Hash::make($data['password']),
                'id_rol' => 3,
            ]);

            Alumno::create([
                'id_user' => $usuario->id_user,
                'ci' => $data['ci'],
                'nombres' => $data['nombres'],
                'ap_paterno' => $data['ap_paterno'],
                'ap_materno' => $data['ap_materno'],
                'genero' => $data['genero'],
                'fecha_nac' => $data['fecha_nac'],
            ]);
        });
    }

    public function actualizarConUsuario(Alumno $alumno, array $data): void
    {
        DB::transaction(function () use ($alumno, $data) {
            $usuario = $alumno->usuario;

            if (!$usuario) {
                $usuario = User::create([
                    'username' => $data['username'],
                    'password' => Hash::make($data['password'] ?: 'alumno123'),
                    'id_rol' => 3,
                ]);

                $alumno->id_user = $usuario->id_user;
            } else {
                $usuario->username = $data['username'];

                if (!empty($data['password'])) {
                    $usuario->password = Hash::make($data['password']);
                }

                $usuario->id_rol = 3;
                $usuario->save();
            }

            $alumno->ci = $data['ci'];
            $alumno->nombres = $data['nombres'];
            $alumno->ap_paterno = $data['ap_paterno'];
            $alumno->ap_materno = $data['ap_materno'];
            $alumno->genero = $data['genero'];
            $alumno->fecha_nac = $data['fecha_nac'];
            $alumno->save();
        });
    }

    public function eliminar(Alumno $alumno): void
    {
        DB::transaction(function () use ($alumno) {
            $usuario = $alumno->usuario;
            $alumno->delete();

            if ($usuario) {
                $usuario->delete();
            }
        });
    }
}
