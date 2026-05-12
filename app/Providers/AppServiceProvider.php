<?php

namespace App\Providers;

use App\Enums\Rol;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
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

        Gate::before(function ($user, string $ability) {
            if ((int) $user->id_rol === Rol::ADMIN->value) {
                return true;
            }

            if (! Schema::hasTable('rol_funcionalidad') || ! Schema::hasTable('funcionalidades')) {
                return null;
            }

            return DB::table('rol_funcionalidad as rf')
                ->join('funcionalidades as f', 'f.id_funcionalidad', '=', 'rf.id_funcionalidad')
                ->where('rf.id_rol', (int) $user->id_rol)
                ->where('f.nombre', $ability)
                ->exists() ?: null;
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

        Gate::define('fichas-medicas', function ($user) {
            return $this->tienePermiso($user, 'admin.fichas-medicas.index');
        });

        Gate::define('permiso-dinamico', function ($user, string $permiso) {
            return $this->tienePermiso($user, $permiso);
        });

        Gate::define('profesor-horario', function ($user) {
            if ($this->tienePermiso($user, 'profesor.horario')) {
                return true;
            }

            $inicio = microtime(true);

            if ((int) $user->id_rol !== Rol::PROFESOR->value) {
                Log::info('[PERF] Gate profesor-horario', [
                    'ms' => round((microtime(true) - $inicio) * 1000, 2),
                    'resultado' => false,
                    'motivo' => 'no_profesor',
                    'id_user' => $user->id_user ?? null,
                ]);

                return false;
            }

            $inicioSchema = microtime(true);
            if (!Schema::hasTable('profesor_permisos') || !Schema::hasTable('profesor') || !Schema::hasColumn('profesor', 'id_user')) {
                Log::info('[PERF] Gate profesor-horario schema', [
                    'ms' => round((microtime(true) - $inicioSchema) * 1000, 2),
                    'id_user' => $user->id_user ?? null,
                ]);

                Log::info('[PERF] Gate profesor-horario', [
                    'ms' => round((microtime(true) - $inicio) * 1000, 2),
                    'resultado' => true,
                    'motivo' => 'schema_incompleto',
                    'id_user' => $user->id_user ?? null,
                ]);

                return true;
            }

            Log::info('[PERF] Gate profesor-horario schema', [
                'ms' => round((microtime(true) - $inicioSchema) * 1000, 2),
                'id_user' => $user->id_user ?? null,
            ]);

            $inicioConsulta = microtime(true);
            $resultado = (bool) DB::table('profesor as p')
                ->join('profesor_permisos as pp', 'pp.id_profesor', '=', 'p.id_profesor')
                ->where('p.id_user', $user->id_user)
                ->value('pp.puede_ver_horario');

            Log::info('[PERF] Gate profesor-horario consulta', [
                'ms' => round((microtime(true) - $inicioConsulta) * 1000, 2),
                'resultado' => $resultado,
                'id_user' => $user->id_user ?? null,
            ]);

            Log::info('[PERF] Gate profesor-horario', [
                'ms' => round((microtime(true) - $inicio) * 1000, 2),
                'resultado' => $resultado,
                'motivo' => 'consulta_permiso',
                'id_user' => $user->id_user ?? null,
            ]);

            return $resultado;
        });
    }

    private function tienePermiso($user, string $permiso): bool
    {
        if ((int) $user->id_rol === Rol::ADMIN->value) {
            return true;
        }

        if (! Schema::hasTable('rol_funcionalidad') || ! Schema::hasTable('funcionalidades')) {
            return false;
        }

        return DB::table('rol_funcionalidad as rf')
            ->join('funcionalidades as f', 'f.id_funcionalidad', '=', 'rf.id_funcionalidad')
            ->where('rf.id_rol', (int) $user->id_rol)
            ->where('f.nombre', $permiso)
            ->exists();
    }
}
