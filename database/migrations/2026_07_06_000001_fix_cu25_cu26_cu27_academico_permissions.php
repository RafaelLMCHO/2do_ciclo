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

        $idModulo = DB::table('modulos')->where('nombre', 'Academico')->value('id_modulo');

        if (! $idModulo) {
            $idModulo = DB::table('modulos')->insertGetId([
                'nombre' => 'Academico',
                'descripcion' => 'Modulo academico',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $funcionalidades = [
            'admin.historial-academico.index' => 'Consultar historial academico',
            'admin.libretas.index' => 'Generar libreta academica',
            'admin.rendimiento-academico.index' => 'Consultar rendimiento academico',
        ];

        $roles = [
            RolEnum::ADMIN->value,
            RolEnum::SECRETARIA->value,
            RolEnum::PROFESOR->value,
            RolEnum::APODERADO->value,
        ];

        foreach ($funcionalidades as $nombre => $descripcion) {
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
            } else {
                $idFuncionalidad = DB::table('funcionalidades')->insertGetId([
                    'id_modulo' => $idModulo,
                    'nombre' => $nombre,
                    'descripcion' => $descripcion,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            foreach ($roles as $idRol) {
                DB::table('rol_funcionalidad')->updateOrInsert([
                    'id_rol' => $idRol,
                    'id_funcionalidad' => $idFuncionalidad,
                ]);
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
    }
};
