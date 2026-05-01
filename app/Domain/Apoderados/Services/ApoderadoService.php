<?php

namespace App\Domain\Apoderados\Services;

use App\Models\Apoderado;
use App\Models\Parentesco;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ApoderadoService
{
    public function resolverPorUsername(string $username): ?object
    {
        $apoderado = DB::table('apoderado')
            ->whereRaw("CONCAT('apoderado_', id_apoderado) = ?", [$username])
            ->first();

        if ($apoderado) {
            return $apoderado;
        }

        if (preg_match('/^apoderado_(\d+)$/', $username, $matches)) {
            return DB::table('apoderado')
                ->where('id_apoderado', (int) $matches[1])
                ->first();
        }

        return null;
    }

    public function obtenerHijosDeApoderado(int $idApoderado): Collection
    {
        return DB::table('parentesco as p')
            ->join('alumno as a', 'a.id_alumno', '=', 'p.id_alumno')
            ->where('p.id_apoderado', $idApoderado)
            ->select(
                'a.id_alumno',
                'a.nombres',
                'a.ap_paterno',
                'a.ap_materno',
                'p.descripcion as parentesco'
            )
            ->orderBy('a.ap_paterno')
            ->orderBy('a.ap_materno')
            ->orderBy('a.nombres')
            ->get()
            ->map(function ($hijo) {
                $hijo->nombre_completo = trim(
                    $hijo->nombres.' '.$hijo->ap_paterno.' '.$hijo->ap_materno
                );
                return $hijo;
            });
    }

    public function obtenerTodosLosHijosConApoderados(): Collection
    {
        return DB::table('alumno as a')
            ->leftJoin('parentesco as p', 'p.id_alumno', '=', 'a.id_alumno')
            ->leftJoin('apoderado as ap', 'ap.id_apoderado', '=', 'p.id_apoderado')
            ->select(
                'a.id_alumno',
                'a.nombres',
                'a.ap_paterno',
                'a.ap_materno',
                DB::raw("
                    GROUP_CONCAT(
                        DISTINCT CONCAT_WS(' ', ap.nombres, ap.ap_paterno, ap.ap_materno)
                        ORDER BY ap.ap_paterno, ap.ap_materno, ap.nombres
                        SEPARATOR ' | '
                    ) as apoderados
                "),
                DB::raw("
                    GROUP_CONCAT(
                        DISTINCT CONCAT_WS(': ', CONCAT_WS(' ', ap.nombres, ap.ap_paterno, ap.ap_materno), p.descripcion)
                        ORDER BY ap.ap_paterno, ap.ap_materno, ap.nombres
                        SEPARATOR ' | '
                    ) as apoderados_detalle
                ")
            )
            ->groupBy('a.id_alumno', 'a.nombres', 'a.ap_paterno', 'a.ap_materno')
            ->orderBy('a.ap_paterno')
            ->orderBy('a.ap_materno')
            ->orderBy('a.nombres')
            ->get()
            ->map(function ($hijo) {
                $hijo->nombre_completo = trim(
                    $hijo->nombres.' '.$hijo->ap_paterno.' '.$hijo->ap_materno
                );
                return $hijo;
            });
    }
}
