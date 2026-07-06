<?php

use App\Enums\Rol as RolEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('modulos') || ! Schema::hasTable('funcionalidades') || ! Schema::hasTable('rol_funcionalidad')) {
            return;
        }

        $permisos = [
            'Academico' => [
                'admin.historial-academico.index' => [
                    'descripcion' => 'Consultar historial academico',
                    'roles' => [RolEnum::ADMIN->value, RolEnum::SECRETARIA->value, RolEnum::PROFESOR->value, RolEnum::APODERADO->value],
                ],
                'admin.libretas.index' => [
                    'descripcion' => 'Generar libreta academica',
                    'roles' => [RolEnum::ADMIN->value, RolEnum::SECRETARIA->value, RolEnum::PROFESOR->value, RolEnum::APODERADO->value],
                ],
                'admin.rendimiento-academico.index' => [
                    'descripcion' => 'Consultar rendimiento academico',
                    'roles' => [RolEnum::ADMIN->value, RolEnum::SECRETARIA->value, RolEnum::PROFESOR->value, RolEnum::APODERADO->value],
                ],
            ],
        ];

        foreach ($permisos as $moduloNombre => $funcionalidades) {
            $idModulo = $this->asegurarModulo($moduloNombre);

            foreach ($funcionalidades as $nombre => $meta) {
                $idFuncionalidad = $this->asegurarFuncionalidad($idModulo, $nombre, $meta['descripcion']);

                foreach ($meta['roles'] as $idRol) {
                    DB::table('rol_funcionalidad')->updateOrInsert([
                        'id_rol' => $idRol,
                        'id_funcionalidad' => $idFuncionalidad,
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('funcionalidades') || ! Schema::hasTable('rol_funcionalidad')) {
            return;
        }

        $nombres = [
            'admin.historial-academico.index',
            'admin.libretas.index',
            'admin.rendimiento-academico.index',
        ];

        $ids = DB::table('funcionalidades')
            ->whereIn('nombre', $nombres)
            ->pluck('id_funcionalidad');

        if ($ids->isEmpty()) {
            return;
        }

        DB::table('rol_funcionalidad')->whereIn('id_funcionalidad', $ids)->delete();
        DB::table('funcionalidades')->whereIn('id_funcionalidad', $ids)->delete();
    }

    private function asegurarModulo(string $nombre): int
    {
        $idModulo = DB::table('modulos')->where('nombre', $nombre)->value('id_modulo');

        if ($idModulo) {
            return (int) $idModulo;
        }

        return (int) DB::table('modulos')->insertGetId([
            'nombre' => $nombre,
            'descripcion' => 'Modulo ' . strtolower($nombre),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function asegurarFuncionalidad(int $idModulo, string $nombre, string $descripcion): int
    {
        $idFuncionalidad = DB::table('funcionalidades')
            ->where('nombre', $nombre)
            ->value('id_funcionalidad');

        if ($idFuncionalidad) {
            DB::table('funcionalidades')
                ->where('id_funcionalidad', $idFuncionalidad)
                ->update([
                    'id_modulo' => $idModulo,
                    'descripcion' => $descripcion,
                    'updated_at' => now(),
                ]);

            return (int) $idFuncionalidad;
        }

        return (int) DB::table('funcionalidades')->insertGetId([
            'id_modulo' => $idModulo,
            'nombre' => $nombre,
            'descripcion' => $descripcion,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
};
