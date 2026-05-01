<?php

namespace App\Domain\Auth\Services;

use App\Enums\Rol;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class AuthService
{
    public function redirectAfterLogin(User $user): string
    {
        if ($this->esProfesorConHorario($user)) {
            return route('profesor.horario');
        }

        if ($this->esApoderado($user)) {
            return route('apoderado.consulta');
        }

        return route('home-panel');
    }

    public function redirectAfterHome(User $user): string
    {
        if ($this->esProfesorConHorario($user)) {
            return route('profesor.horario');
        }

        return route('home-panel');
    }

    public function esAdmin(User $user): bool
    {
        return (int) $user->id_rol === Rol::ADMIN->value;
    }

    public function esProfesor(User $user): bool
    {
        return (int) $user->id_rol === Rol::PROFESOR->value;
    }

    public function esAlumno(User $user): bool
    {
        return (int) $user->id_rol === Rol::ALUMNO->value;
    }

    public function esApoderado(User $user): bool
    {
        return (int) $user->id_rol === Rol::APODERADO->value;
    }

    public function esProfesorConHorario(User $user): bool
    {
        return $this->esProfesor($user) && Gate::allows('profesor-horario');
    }

    public function labelDelRol(User $user): string
    {
        $rol = Rol::tryFrom((int) $user->id_rol);
        return $rol ? $rol->label() : 'Usuario';
    }
}