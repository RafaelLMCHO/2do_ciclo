<?php

namespace App\Models;

use App\Enums\Rol;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'usuario';
    protected $primaryKey = 'id_user';
    public $timestamps = false;

    protected $fillable = [
        'username',
        'password',
        'id_rol',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function rol(): Rol
    {
        return Rol::tryFrom((int) $this->id_rol) ?? Rol::ADMIN;
    }

    public function getRolNombreAttribute(): string
    {
        return $this->rol()->label();
    }

    public function isAdmin(): bool
    {
        return $this->rol()->isAdmin();
    }

    public function isProfesor(): bool
    {
        return $this->rol()->isProfesor();
    }

    public function isAlumno(): bool
    {
        return $this->rol()->isAlumno();
    }

    public function isApoderado(): bool
    {
        return $this->rol()->isApoderado();
    }
}
