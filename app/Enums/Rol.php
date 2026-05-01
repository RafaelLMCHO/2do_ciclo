<?php

namespace App\Enums;

enum Rol: int
{
    case ADMIN = 1;
    case PROFESOR = 2;
    case ALUMNO = 3;
    case APODERADO = 4;
    case DIRECTOR = 5;
    case SECRETARIA = 6;

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Administrador',
            self::PROFESOR => 'Profesor',
            self::ALUMNO => 'Alumno',
            self::APODERADO => 'Apoderado',
            self::DIRECTOR => 'Director',
            self::SECRETARIA => 'Secretaria',
        };
    }

    public function isAdmin(): bool
    {
        return $this === self::ADMIN;
    }

    public function isProfesor(): bool
    {
        return $this === self::PROFESOR;
    }

    public function isAlumno(): bool
    {
        return $this === self::ALUMNO;
    }

    public function isApoderado(): bool
    {
        return $this === self::APODERADO;
    }
}
