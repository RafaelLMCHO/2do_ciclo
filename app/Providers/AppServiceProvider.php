<?php

namespace App\Providers;

use App\Enums\Rol;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Schema::defaultStringLength(191);

        Gate::before(function ($user) {
            if ((int) $user->id_rol === Rol::ADMIN->value) {
                return true;
            }
        });

        Gate::define('is-admin', function ($user) {
            return (int) $user->id_rol === Rol::ADMIN->value;
        });

        Gate::define('is-profesor', function ($user) {
            return (int) $user->id_rol === Rol::PROFESOR->value;
        });

        Gate::define('is-apoderado', function ($user) {
            return (int) $user->id_rol === Rol::APODERADO->value;
        });

        Gate::define('profesor-horario', function ($user) {
            if ((int) $user->id_rol !== Rol::PROFESOR->value) {
                return false;
            }

            if (!Schema::hasTable('profesor_permisos') || !Schema::hasTable('profesor') || !Schema::hasColumn('profesor', 'id_user')) {
                return true;
            }

            return (bool) DB::table('profesor as p')
                ->join('profesor_permisos as pp', 'pp.id_profesor', '=', 'p.id_profesor')
                ->where('p.id_user', $user->id_user)
                ->value('pp.puede_ver_horario');
        });
    }
}
